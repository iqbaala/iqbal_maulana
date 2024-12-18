# WellBe - Aplikasi Gaya Hidup Sehat

WellBe adalah aplikasi gaya hidup sehat yang dirancang untuk membantu pengguna menjaga keseimbangan kesehatan tubuh dan pikiran. Dengan pendekatan holistik, WellBe memadukan pelacakan nutrisi, olahraga, dan kualitas tidur.

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MongoDB 4.4 atau lebih tinggi
- Composer
- Web Server (Apache/Nginx)
- MongoDB PHP Extension

## Instalasi

1. Clone repositori ini:
bash
git clone https://github.com/username/wellbe.git
cd wellbe
```

2. Install PHP MongoDB Extension:
```bash
# Ubuntu/Debian
sudo pecl install mongodb
sudo echo "extension=mongodb.so" > /etc/php/7.4/mods-available/mongodb.ini
sudo phpenmod mongodb

# Windows dengan XAMPP
# Unduh DLL yang sesuai dari https://pecl.php.net/package/mongodb
# Letakkan di folder ext PHP dan tambahkan extension=mongodb di php.ini
```

3. Install dependensi menggunakan Composer:
```bash
composer install
```

4. Salin file .env.example ke .env dan sesuaikan konfigurasi:
```bash
cp .env.example .env
```

5. Pastikan MongoDB berjalan di sistem Anda:
```bash
# Ubuntu/Debian
sudo systemctl start mongod
sudo systemctl enable mongod

# Windows
# Jalankan MongoDB service melalui Services atau
# "C:\Program Files\MongoDB\Server\4.4\bin\mongod.exe"
```

6. Buat database MongoDB:
```bash
mongosh
use wellbe
```

7. Atur permission folder:
```bash
# Linux/Mac
chmod -R 755 .
chmod -R 777 storage
```

8. Jalankan server development:
```bash
php -S localhost:8000 -t public
```

## Penggunaan

1. Buka browser dan akses `http://localhost:8000`
2. Register akun baru atau login jika sudah memiliki akun
3. Mulai menggunakan fitur-fitur WellBe:
   - Pelacakan Nutrisi
   - Aktivitas Fisik
   - Monitor Tidur
   - Statistik & Analisis
   - Tips & Edukasi

## Struktur Direktori

```
wellbe/
├── config/               # Konfigurasi aplikasi
│   └── database.php     # Konfigurasi database
├── public/              # Public files
│   ├── assets/         # Asset statis (CSS, JS, images)
│   └── index.php       # Entry point aplikasi
├── src/                 # Source code
│   ├── controllers/    # Controllers
│   ├── models/        # Models
│   └── views/         # Views
├── vendor/             # Dependencies (dikelola Composer)
├── .env               # Environment variables
├── .gitignore        # Git ignore rules
├── composer.json     # Composer configuration
└── README.md        # Dokumentasi
```

## Fitur

1. Pelacakan Nutrisi
   - Catat asupan makanan dan minuman
   - Perhitungan kalori otomatis
   - Rekomendasi nutrisi personal

2. Aktivitas Fisik
   - Rekam jenis olahraga
   - Monitor durasi dan intensitas
   - Tantangan mingguan

3. Monitor Tidur
   - Catat durasi tidur
   - Analisis kualitas tidur
   - Rekomendasi waktu tidur

4. Dashboard Terpadu
   - Ringkasan harian
   - Grafik progress
   - Statistik kesehatan

5. Tips & Edukasi
   - Artikel kesehatan
   - Video tutorial
   - Rekomendasi gaya hidup sehat

## Troubleshooting

1. Error koneksi MongoDB:
   - Pastikan service MongoDB berjalan
   - Cek konfigurasi di file .env
   - Verifikasi kredensial database

2. Error 500:
   - Cek error log PHP
   - Pastikan semua extension terinstal
   - Verifikasi permission folder

3. Autoloader error:
   - Jalankan `composer dump-autoload`
   - Cek namespace di composer.json

## Kontribusi

1. Fork repositori
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Lisensi

[MIT License](LICENSE)

## Kontak

WellBe Team - team@wellbe.com``` 