import io
import os
import unittest
from flask_service.app import app

class WasteWiseFlaskTestCase(unittest.TestCase):
    def setUp(self):
        app.config['TESTING'] = True
        app.config['SECRET_KEY'] = 'test-secret-key'
        app.config['WTF_CSRF_ENABLED'] = False
        self.client = app.test_client()

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

    def test_05_post_insights_form_focus_electronic(self):
        response = self.client.post('/insights', data={
            'focus': 'electronic'
        }, follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Electronic Waste', response.data)
        self.assertIn(b'Surat', response.data)
        self.assertIn(b'Electronic Waste is currently highly searched in Surat', response.data)

    def test_06_post_insights_form_custom_category(self):
        response = self.client.post('/insights', data={
            'category': 'Hazardous Waste',
            'city': 'Rajkot',
            'searches': '35'
        }, follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Hazardous Waste', response.data)
        self.assertIn(b'Rajkot', response.data)

    def test_07_post_api_analyze_json_success(self):
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

    def test_08_post_api_analyze_json_invalid(self):
        response = self.client.post('/api/analyze', json={"category": "Plastic Waste"})
        self.assertEqual(response.status_code, 422)
        json_data = response.get_json()
        self.assertFalse(json_data['success'])

    def test_09_session_storage_and_result_page(self):
        with self.client as c:
            c.post('/insights', data={
                'focus': 'organic'
            }, follow_redirects=False)
            from flask import session
            self.assertEqual(session.get('last_category'), 'Organic Waste')
            self.assertEqual(session.get('last_city'), 'Ahmedabad')
            self.assertEqual(session.get('last_searches'), 25)

            res = c.get('/insights/result')
            self.assertEqual(res.status_code, 200)
            self.assertIn(b'Organic Waste', res.data)
            self.assertIn(b'Ahmedabad', res.data)

    def test_10_csv_upload_valid(self):
        csv_data = "Category,City,Searches\nPlastic Waste,Surat,20\nOrganic Waste,Surat,15\nPlastic Waste,Surat,30\n"
        data = {
            'file': (io.BytesIO(csv_data.encode('utf-8')), 'test_analytics.csv')
        }
        response = self.client.post('/upload-csv', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'processed successfully', response.data)
        self.assertIn(b'3 data rows', response.data)

    def test_11_csv_upload_invalid_extension(self):
        data = {
            'file': (io.BytesIO(b'malicious content'), 'script.sh')
        }
        response = self.client.post('/upload-csv', data=data, content_type='multipart/form-data', follow_redirects=True)
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Only .csv files are permitted', response.data)

    def test_12_demo_http_methods(self):
        head_res = self.client.head('/api/demo-methods')
        self.assertEqual(head_res.status_code, 200)
        self.assertIn('X-WasteWise-Service', head_res.headers)

        put_res = self.client.put('/api/demo-methods', json={'test': 123})
        self.assertEqual(put_res.status_code, 200)
        self.assertEqual(put_res.get_json()['method'], 'PUT')

        delete_res = self.client.delete('/api/demo-methods')
        self.assertEqual(delete_res.status_code, 200)
        self.assertEqual(delete_res.get_json()['method'], 'DELETE')

    def test_13_csv_highest_search_count_surat_bugfix(self):
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

if __name__ == '__main__':
    unittest.main()
