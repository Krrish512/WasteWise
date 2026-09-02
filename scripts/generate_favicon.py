#!/usr/bin/env python3
import os
import zlib
import struct

def create_png_bytes(w, h, bg_rgb, fg_rgb):
    rows = []
    r1, g1, b1 = bg_rgb
    r2, g2, b2 = fg_rgb
    for y in range(h):
        row = bytearray([0])
        for x in range(w):
            rad = w // 4
            dx = rad - x if x < rad else (x - (w - rad - 1) if x >= w - rad else 0)
            dy = rad - y if y < rad else (y - (h - rad - 1) if y >= h - rad else 0)
            if dx*dx + dy*dy > rad*rad:
                row.extend([0, 0, 0, 0])
            else:
                nx, ny = x / w, y / h
                is_leaf = False
                if 0.2 <= nx <= 0.8 and 0.2 <= ny <= 0.8:
                    if abs((nx - 0.2) - (0.8 - ny)) < 0.28:
                        is_leaf = True
                if is_leaf:
                    row.extend([r2, g2, b2, 255])
                else:
                    row.extend([r1, g1, b1, 255])
        rows.append(bytes(row))
    raw = b''.join(rows)

    def chunk(t, d):
        return struct.pack('>I', len(d)) + t + d + struct.pack('>I', zlib.crc32(t + d) & 0xffffffff)

    return (
        b'\x89PNG\r\n\x1a\n'
        + chunk(b'IHDR', struct.pack('>IIBBBBB', w, h, 8, 6, 0, 0, 0))
        + chunk(b'IDAT', zlib.compress(raw, 9))
        + chunk(b'IEND', b'')
    )

def main():
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    public_dir = os.path.join(base_dir, 'public')

    svg_content = """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64">
  <defs>
    <linearGradient id="wastewise-grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#16a34a" />
      <stop offset="100%" stop-color="#0d9488" />
    </linearGradient>
  </defs>
  <rect width="64" height="64" rx="16" fill="url(#wastewise-grad)" />
  <path d="M44 16 C30 16 18 26 18 41 C18 45 21 48 26 48 C40 48 48 34 48 20 C48 17.8 46.2 16 44 16 Z" fill="#ffffff" />
  <path d="M22 44 C 30 36, 38 28, 44 20" stroke="#15803d" stroke-width="3.5" stroke-linecap="round" fill="none" />
</svg>"""

    svg_path = os.path.join(public_dir, 'favicon.svg')
    with open(svg_path, 'w', encoding='utf-8') as f:
        f.write(svg_content)

    png_32 = create_png_bytes(32, 32, (22, 163, 74), (255, 255, 255))
    png_path = os.path.join(public_dir, 'favicon.png')
    with open(png_path, 'wb') as f:
        f.write(png_32)

    ico_header = struct.pack('<HHH', 0, 1, 1)
    ico_entry = struct.pack('<BBBBHHII', 32, 32, 0, 0, 1, 32, len(png_32), 6 + 16)
    ico_bytes = ico_header + ico_entry + png_32

    ico_path = os.path.join(public_dir, 'favicon.ico')
    with open(ico_path, 'wb') as f:
        f.write(ico_bytes)

    print("Created favicon.svg, favicon.png, and favicon.ico in public/")

if __name__ == "__main__":
    main()
