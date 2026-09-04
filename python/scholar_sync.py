import requests
from bs4 import BeautifulSoup
import mysql.connector
from datetime import datetime
import sys
from urllib.parse import urlparse, parse_qs

# Koneksi ke database Laravel
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="website_dosen_manhut"
)
cursor = db.cursor(dictionary=True)

user_ids_arg = sys.argv[1] if len(sys.argv) > 1 else None

query = """
    SELECT u.id, dp.scholar_link 
    FROM users u
    JOIN profile_dosen dp ON u.id = dp.user_id
    WHERE u.role = 'dosen' 
    AND dp.scholar_link IS NOT NULL 
    AND dp.scholar_link != ''
"""

if user_ids_arg and user_ids_arg != 'all':
    valid_ids = [str(int(x.strip())) for x in user_ids_arg.split(',') if x.strip().isdigit()]
    if valid_ids:
        query += f" AND u.id IN ({','.join(valid_ids)})"

cursor.execute(query)
dosen_list = cursor.fetchall()

now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

for dosen in dosen_list:
    try:
        parsed_url = urlparse(dosen['scholar_link'])
        query_params = parse_qs(parsed_url.query)
        
        if 'user' not in query_params:
            continue
            
        scholar_id = query_params['user'][0]
        print(f"Menarik data Google Scholar untuk ID: {scholar_id} (Database ID: {dosen['id']})...")
        
        # Tembak URL dengan parameter pagesize=100 agar langsung dapat 100 jurnal pertama
        url = f"https://scholar.google.com/citations?user={scholar_id}&hl=en&cstart=0&pagesize=100"
        headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'}
        response = requests.get(url, headers=headers)
        
        # Bedah HTML-nya
        soup = BeautifulSoup(response.text, 'html.parser')
        
        # Cari semua baris tabel publikasi
        rows = soup.find_all('tr', class_='gsc_a_tr')
        
        if not rows:
            print(f"Akun kosong/tidak ditemukan untuk ID {scholar_id}. Di-skip.\n")
            continue

        # Sapu bersih publikasi lama
        cursor.execute("DELETE FROM publications WHERE user_id = %s", (dosen['id'],))

        insert_pub_query = """
            INSERT INTO publications 
            (user_id, title, authors, publisher, cited_by, year, created_at, updated_at) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """
        
        for row in rows:
            # Ambil Judul
            title_element = row.find('a', class_='gsc_a_at')
            title = title_element.text if title_element else ''
            
            # Ambil dua baris abu-abu (Author dan Publisher)
            gray_divs = row.find_all('div', class_='gs_gray')
            authors = gray_divs[0].text if len(gray_divs) > 0 else ''
            publisher = gray_divs[1].text if len(gray_divs) > 1 else ''
            
            # Ambil Sitasi
            cited_by_element = row.find('a', class_='gsc_a_ac')
            cited_by = cited_by_element.text.strip() if cited_by_element and cited_by_element.text.strip() else '0'
            cited_by = int(cited_by) if cited_by.isdigit() else 0
            
            # Ambil Tahun   
            year_element = row.find('span', class_='gsc_a_h')
            year = year_element.text.strip() if year_element and year_element.text.strip() else None
            
            cursor.execute(insert_pub_query, (dosen['id'], title, authors, publisher, cited_by, year, now, now))
            
        db.commit()
        print(f"Sukses! {len(rows)} jurnal dosen ID {dosen['id']} berhasil dimasukkan secepat kilat.\n")
        
    except Exception as e:
        print(f"Gagal narik data untuk ID {dosen['id']}: {e}\n")
        db.rollback() 

cursor.close()
db.close()