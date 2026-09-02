import io
import json
import os
import sys
import unittest
from unittest.mock import patch, MagicMock

from flask import session

# Ensure parent directory (wastewise) and local directory are in sys.path
base_dir = os.path.dirname(os.path.abspath(__file__))
parent_dir = os.path.dirname(base_dir)
if parent_dir not in sys.path:
    sys.path.insert(0, parent_dir)
if base_dir not in sys.path:
    sys.path.insert(0, base_dir)

from flask_service.app import app, analyze_search_records, parse_and_validate_collection_centre_csv


class WasteWiseFlaskTestCase(unittest.TestCase):
    def setUp(self):
        app.config['TESTING'] = True
        app.config['SECRET_KEY'] = 'test-secret-key'
        app.config['WTF_CSRF_ENABLED'] = False
        self.client = app.test_client()

    # ─── Basic Route Tests ──────────────────────────────────────────────

    def test_01_get_home_redirects_to_insights(self):
        response = self.client.get('/', follow_redirects=False)
        self.assertEqual(response.status_code, 302)
        self.assertIn('/insights', response.headers['Location'])

    def test_02_health_check_json(self):
        response = self.client.get('/health')
        self.assertEqual(response.status_code, 200)
        json_data = response.get_json()
        self.assertEqual(json_data['status'], 'ok')
        self.assertEqual(json_data['service'], 'WasteWise Python Insights')

    def test_03_get_insights_page(self):
        response = self.client.get('/insights')
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'WasteWise Python Insights', response.data)
        self.assertIn(b'Analyze WasteWise Data', response.data)

    def test_04_get_insights_args_prefill(self):
        response = self.client.get('/insights?focus=electronic')
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Electronic Waste Focus', response.data)

    # ─── Real Data Analysis (Mocked Laravel API) ────────────────────────

    @patch('flask_service.app.fetch_laravel_search_trends')
    def test_05_post_insights_real_data_electronic(self, mock_fetch):
        """POST with focus=electronic fetches real data and computes analysis."""
        mock_fetch.return_value = {
            'success': True,
            'total_searches': 3,
            'focus': 'electronic',
            'records': [
                {'waste_item': 'Old Laptop', 'category': 'Electronic Waste', 'searched_at': '2026-09-01 10:00:00'},
                {'waste_item': 'Old Mobile', 'category': 'Electronic Waste', 'searched_at': '2026-09-01 11:00:00'},
                {'waste_item': 'Old Laptop', 'category': 'Electronic Waste', 'searched_at': '2026-09-01 12:00:00'},
            ]
        }
        response = self.client.post('/insights', data={'focus': 'electronic'}, follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Electronic Waste', response.data)
        self.assertIn(b'Old Laptop', response.data)
        self.assertIn(b'3', response.data)
        mock_fetch.assert_called_once_with('electronic')

    @patch('flask_service.app.fetch_laravel_search_trends')
    def test_06_post_insights_real_data_all(self, mock_fetch):
        """POST with focus=all fetches all data and computes correct top category."""
        mock_fetch.return_value = {
            'success': True,
            'total_searches': 5,
            'focus': 'all',
            'records': [
                {'waste_item': 'Plastic Bottle', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 10:00:00'},
                {'waste_item': 'Plastic Bag', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 11:00:00'},
                {'waste_item': 'Plastic Bottle', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 12:00:00'},
                {'waste_item': 'Old Laptop', 'category': 'Electronic Waste', 'searched_at': '2026-09-01 13:00:00'},
                {'waste_item': 'Banana Peel', 'category': 'Organic Waste', 'searched_at': '2026-09-01 14:00:00'},
            ]
        }
        response = self.client.post('/insights', data={'focus': 'all'}, follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Plastic Waste', response.data)
        self.assertIn(b'Plastic Bottle', response.data)
        self.assertIn(b'5', response.data)

    @patch('flask_service.app.fetch_laravel_search_trends')
    def test_07_post_insights_empty_data(self, mock_fetch):
        """Empty search history shows a meaningful 'no data' message."""
        mock_fetch.return_value = {
            'success': True,
            'total_searches': 0,
            'focus': 'all',
            'records': []
        }
        response = self.client.post('/insights', data={'focus': 'all'}, follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'No search history data available', response.data)
        self.assertIn(b'0', response.data)

    @patch('flask_service.app.fetch_laravel_search_trends')
    def test_08_post_insights_laravel_unavailable(self, mock_fetch):
        """Graceful failure when Laravel API is unreachable."""
        mock_fetch.return_value = None
        response = self.client.post('/insights', data={'focus': 'all'}, follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Could not connect to the main WasteWise application', response.data)

    @patch('flask_service.app.fetch_laravel_search_trends')
    def test_09_session_stores_real_analysis(self, mock_fetch):
        """Session keys are correctly populated from real data analysis."""
        mock_fetch.return_value = {
            'success': True,
            'total_searches': 2,
            'focus': 'plastic',
            'records': [
                {'waste_item': 'Plastic Bottle', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 10:00:00'},
                {'waste_item': 'Plastic Bag', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 11:00:00'},
            ]
        }
        with self.client as c:
            c.post('/insights', data={'focus': 'plastic'}, follow_redirects=False)
            self.assertEqual(session.get('last_category'), 'Plastic Waste')
            self.assertEqual(session.get('last_item'), 'Plastic Bottle')
            self.assertEqual(session.get('last_searches'), 2)
            self.assertEqual(session.get('analysis_source'), 'WasteWise Live Data')
            self.assertEqual(session.get('analysis_focus'), 'plastic')
            self.assertIsNotNone(session.get('last_insight'))
            self.assertIsInstance(session.get('category_breakdown'), list)

            res = c.get('/insights/result')
            self.assertEqual(res.status_code, 200)
            self.assertIn(b'Plastic Waste', res.data)

    @patch('flask_service.app.fetch_laravel_search_trends')
    def test_10_category_breakdown_computed(self, mock_fetch):
        """Category breakdown percentages are correctly computed."""
        mock_fetch.return_value = {
            'success': True,
            'total_searches': 4,
            'focus': 'all',
            'records': [
                {'waste_item': 'Plastic Bottle', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 10:00:00'},
                {'waste_item': 'Old Laptop', 'category': 'Electronic Waste', 'searched_at': '2026-09-01 11:00:00'},
                {'waste_item': 'Plastic Bag', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 12:00:00'},
                {'waste_item': 'Plastic Bottle', 'category': 'Plastic Waste', 'searched_at': '2026-09-01 13:00:00'},
            ]
        }
        response = self.client.post('/insights', data={'focus': 'all'}, follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        # Plastic Waste = 3/4 = 75.0%
        self.assertIn(b'75.0%', response.data)
        # Electronic Waste = 1/4 = 25.0%
        self.assertIn(b'25.0%', response.data)
        self.assertIn(b'Category Breakdown', response.data)

    # ─── Pure Python Analysis Unit Tests ────────────────────────────────

    def test_11_analyze_search_records_empty(self):
        """analyze_search_records handles empty list."""
        result = analyze_search_records([])
        self.assertEqual(result['total_searches'], 0)
        self.assertEqual(result['top_category'], 'N/A')
        self.assertEqual(result['top_item'], 'N/A')
        self.assertIn('No search history', result['insight'])

    def test_12_analyze_search_records_correct_top(self):
        """analyze_search_records correctly identifies top category and item."""
        records = [
            {'waste_item': 'Old Laptop', 'category': 'Electronic Waste'},
            {'waste_item': 'Old Mobile', 'category': 'Electronic Waste'},
            {'waste_item': 'Plastic Bottle', 'category': 'Plastic Waste'},
            {'waste_item': 'Old Laptop', 'category': 'Electronic Waste'},
            {'waste_item': 'Banana Peel', 'category': 'Organic Waste'},
        ]
        result = analyze_search_records(records)
        self.assertEqual(result['total_searches'], 5)
        self.assertEqual(result['top_category'], 'Electronic Waste')
        self.assertEqual(result['top_item'], 'Old Laptop')
        self.assertEqual(len(result['category_breakdown']), 3)
        # Electronic = 3/5 = 60%
        e_row = next(r for r in result['category_breakdown'] if r['category'] == 'Electronic Waste')
        self.assertEqual(e_row['searches'], 3)
        self.assertEqual(e_row['percentage'], 60.0)

    def test_13_analyze_search_records_single_record(self):
        """analyze_search_records handles a single record."""
        records = [{'waste_item': 'Newspaper', 'category': 'Paper Waste'}]
        result = analyze_search_records(records)
        self.assertEqual(result['total_searches'], 1)
        self.assertEqual(result['top_category'], 'Paper Waste')
        self.assertEqual(result['top_item'], 'Newspaper')
        self.assertEqual(result['category_breakdown'][0]['percentage'], 100.0)

    # ─── CSV Upload Tests (Preserved) ───────────────────────────────────

    def test_14_csv_upload_valid(self):
        csv_data = "Category,City,Searches\nPlastic Waste,Surat,20\nOrganic Waste,Surat,15\nPlastic Waste,Surat,30\n"
        data = {
            'file': (io.BytesIO(csv_data.encode('utf-8')), 'test_analytics.csv')
        }
        response = self.client.post('/upload-csv', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'processed successfully', response.data)
        self.assertIn(b'3 data rows', response.data)

    def test_15_csv_upload_invalid_extension(self):
        data = {
            'file': (io.BytesIO(b'malicious content'), 'script.sh')
        }
        response = self.client.post('/upload-csv', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Only .csv files are permitted', response.data)

    def test_16_csv_highest_search_count_surat_bugfix(self):
        csv_content = (
            "waste_category,city_name,search_count\n"
            "Plastic Waste,Surat,15\n"
            "Electronic Waste,Surat,20\n"
            "Organic Waste,Surat,12\n"
            "Paper Waste,Surat,10\n"
            "Glass Waste,Surat,8\n"
            "Metal Waste,Surat,6\n"
        )
        data = {
            'file': (io.BytesIO(csv_content.encode('utf-8')), 'surat_analytics.csv')
        }
        response = self.client.post('/upload-csv', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Electronic Waste', response.data)
        self.assertIn(b'Surat', response.data)
        self.assertIn(b'20', response.data)
        self.assertIn(b'Electronic Waste is currently highly searched in Surat', response.data)

    # ─── JSON API Tests (Preserved) ─────────────────────────────────────

    def test_17_post_api_analyze_json_success(self):
        response = self.client.post('/api/analyze', json={
            'category': 'Electronic Waste',
            'city': 'Ahmedabad',
            'searches': 55
        })
        self.assertEqual(response.status_code, 200)
        json_data = response.get_json()
        self.assertTrue(json_data['success'])
        self.assertEqual(json_data['category'], 'Electronic Waste')
        self.assertEqual(json_data['city'], 'Ahmedabad')
        self.assertEqual(json_data['searches'], 55)
        self.assertIn('extremely high search volume', json_data['insight'])

    def test_18_post_api_analyze_json_invalid(self):
        response = self.client.post('/api/analyze', json={"category": "Plastic Waste"})
        self.assertEqual(response.status_code, 422)
        json_data = response.get_json()
        self.assertFalse(json_data['success'])

    # ─── HTTP Method Syllabus Tests (Preserved) ─────────────────────────

    def test_19_demo_http_methods(self):
        head_res = self.client.head('/api/demo-methods')
        self.assertEqual(head_res.status_code, 200)
        self.assertIn('X-WasteWise-Service', head_res.headers)

        put_res = self.client.put('/api/demo-methods', json={'test': 123})
        self.assertEqual(put_res.status_code, 200)
        self.assertEqual(put_res.get_json()['method'], 'PUT')

        delete_res = self.client.delete('/api/demo-methods')
        self.assertEqual(delete_res.status_code, 200)
        self.assertEqual(delete_res.get_json()['method'], 'DELETE')

    # ─── Result Page Without Session (Edge Case) ────────────────────────

    def test_20_result_page_no_session_redirects(self):
        """Visiting /insights/result without session data redirects to /insights."""
        response = self.client.get('/insights/result', follow_redirects=False)
        self.assertEqual(response.status_code, 302)
        self.assertIn('/insights', response.headers['Location'])

    # ─── Collection Centre CSV Import Tests ──────────────────────────────

    def test_21_parse_collection_centre_csv_helper_valid(self):
        """Test parse_and_validate_collection_centre_csv with valid CSV."""
        import tempfile

        csv_content = (
            "name,category,city,address,latitude,longitude,phone\n"
            "Surat Hub,Electronic Waste,Surat,Ring Road,21.17,72.83,9876543210\n"
            "Ahmedabad Depot,Plastic Waste,Ahmedabad,CG Road,23.02,72.57,9876543211\n"
        )
        with tempfile.NamedTemporaryFile(mode='w', suffix='.csv', delete=False, encoding='utf-8') as tf:
            tf.write(csv_content)
            temp_path = tf.name

        try:
            rows, errors = parse_and_validate_collection_centre_csv(temp_path)
            self.assertEqual(len(rows), 2)
            self.assertEqual(len(errors), 0)
            self.assertEqual(rows[0]['name'], 'Surat Hub')
            self.assertEqual(rows[0]['city'], 'Surat')
            self.assertEqual(rows[0]['latitude'], 21.17)
        finally:
            os.remove(temp_path)

    def test_22_parse_collection_centre_csv_helper_missing_headers(self):
        """Test parse_and_validate_collection_centre_csv with missing headers."""
        import tempfile

        csv_content = "name,phone\nSurat Hub,9876543210\n"
        with tempfile.NamedTemporaryFile(mode='w', suffix='.csv', delete=False, encoding='utf-8') as tf:
            tf.write(csv_content)
            temp_path = tf.name

        try:
            rows, errors = parse_and_validate_collection_centre_csv(temp_path)
            self.assertIsNone(rows)
            self.assertTrue(any('Missing required CSV headers' in err for err in errors))
        finally:
            os.remove(temp_path)

    def test_23_parse_collection_centre_csv_helper_invalid_lat_lng(self):
        """Test parse_and_validate_collection_centre_csv with invalid lat/lng."""
        import tempfile

        csv_content = (
            "name,category,city,address,latitude,longitude,phone\n"
            "Bad Lat Hub,Electronic Waste,Surat,Ring Road,999,72.83,9876543210\n"
        )
        with tempfile.NamedTemporaryFile(mode='w', suffix='.csv', delete=False, encoding='utf-8') as tf:
            tf.write(csv_content)
            temp_path = tf.name

        try:
            rows, errors = parse_and_validate_collection_centre_csv(temp_path)
            self.assertTrue(any('Latitude must be between -90 and 90' in err for err in errors))
        finally:
            os.remove(temp_path)

    @patch('urllib.request.urlopen')
    def test_24_import_collection_centres_route_success(self, mock_urlopen):
        """Test /import-collection-centres POST route when Laravel API succeeds."""
        mock_response = MagicMock()
        mock_response.read.return_value = json.dumps({
            'success': True,
            'imported': 2,
            'updated': 0,
            'failed': 0
        }).encode('utf-8')
        mock_urlopen.return_value.__enter__.return_value = mock_response

        csv_content = (
            "name,category,city,address,latitude,longitude,phone\n"
            "Surat Hub,Electronic Waste,Surat,Ring Road,21.17,72.83,9876543210\n"
        )
        data = {
            'file': (io.BytesIO(csv_content.encode('utf-8')), 'centres.csv')
        }
        response = self.client.post('/import-collection-centres', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Collection Centres imported successfully', response.data)
        self.assertIn(b'2 new centres created', response.data)

    @patch('urllib.request.urlopen')
    def test_25_import_collection_centres_route_laravel_validation_error(self, mock_urlopen):
        """Test /import-collection-centres POST route when Laravel API returns 422 error."""
        import urllib.error
        err_body = json.dumps({
            'success': False,
            'imported': 0,
            'updated': 0,
            'failed': 1,
            'errors': [{'row': 1, 'field': 'city', 'message': "City 'Nonexistent' not found in database."}]
        }).encode('utf-8')

        fp = io.BytesIO(err_body)
        mock_err = urllib.error.HTTPError('http://127.0.0.1:8000/api/admin/collection-centres/import', 422, 'Unprocessable Entity', {}, fp)
        mock_urlopen.side_effect = mock_err

        csv_content = (
            "name,category,city,address\n"
            "Bad Hub,Electronic Waste,Nonexistent,Ring Road\n"
        )
        data = {
            'file': (io.BytesIO(csv_content.encode('utf-8')), 'bad_centres.csv')
        }
        response = self.client.post('/import-collection-centres', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Laravel Validation Error', response.data)
        self.assertIn(b'Nonexistent', response.data)

    def test_26_import_collection_centres_invalid_extension(self):
        """Test /import-collection-centres with non-csv file extension."""
        data = {
            'file': (io.BytesIO(b'malicious content'), 'script.sh')
        }
        response = self.client.post('/import-collection-centres', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Only .csv files are permitted', response.data)

    def test_27_download_sample_csv_route(self):
        """Test /download-sample-csv route serves the sample CSV file."""
        response = self.client.get('/download-sample-csv')
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Surat E-Waste Recycling Hub', response.data)
        self.assertEqual(response.headers['Content-Disposition'], 'attachment; filename=sample_collection_centres.csv')


if __name__ == '__main__':
    unittest.main()
