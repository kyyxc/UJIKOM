# Image Files Setup Guide

## Required Image Files

Seeder akan menggunakan gambar lokal dari storage. Pastikan file-file berikut ada di storage Anda:

### Hotel Images
Lokasi: `storage/app/public/hotels/`

File yang dibutuhkan:
- `hotel1.jpg` sampai `hotel10.jpg`

Setiap hotel akan mendapat **3-5 gambar random** dari pool 10 gambar ini.

### Room Images
Lokasi: `storage/app/public/rooms/`

File yang dibutuhkan:
- `room1.jpg` sampai `room20.jpg`

Setiap kamar akan mendapat:
- **Suite**: 5 gambar random
- **Single/Double/Deluxe**: 3 gambar random

## Setup Instructions

### 1. Buat Direktori
```bash
mkdir -p storage/app/public/hotels
mkdir -p storage/app/public/rooms
```

### 2. Tambahkan File Gambar
Letakkan file gambar hotel (hotel1.jpg - hotel10.jpg) di folder `storage/app/public/hotels/`
Letakkan file gambar room (room1.jpg - room20.jpg) di folder `storage/app/public/rooms/`

### 3. Buat Symbolic Link
```bash
php artisan storage:link
```

Ini akan membuat symbolic link dari `storage/app/public` ke `public/storage`

### 4. Jalankan Seeder
```bash
php artisan migrate:fresh --seed
```

## Hasil Seeder

Berdasarkan seeder terakhir:
- ✅ **23 hotels** dibuat dengan **87-115 hotel images** (3-5 per hotel)
- ✅ **432 rooms** dibuat dengan **1,418 room images** (3-5 per room)
- ✅ Semua gambar menggunakan path lokal: `hotels/hotelX.jpg` dan `rooms/roomX.jpg`

## Mengakses Gambar

Dari frontend/API, gambar dapat diakses melalui:

```
http://your-domain.com/storage/hotels/hotel1.jpg
http://your-domain.com/storage/rooms/room1.jpg
```

Atau dalam Laravel:
```php
Storage::url('hotels/hotel1.jpg')  // Returns: /storage/hotels/hotel1.jpg
Storage::url('rooms/room1.jpg')    // Returns: /storage/rooms/room1.jpg
```

## Tips

1. **Format Gambar**: Gunakan JPG untuk ukuran file lebih kecil
2. **Resolusi Recommended**: 
   - Hotel images: 1920x1080 atau 1600x900
   - Room images: 1280x720 atau 1024x768
3. **Ukuran File**: Usahakan di bawah 500KB per gambar untuk performa optimal
4. **Nama File**: Jangan ubah nama file (hotel1.jpg - hotel10.jpg, room1.jpg - room20.jpg)

## Troubleshooting

### Gambar tidak muncul?
1. Pastikan `php artisan storage:link` sudah dijalankan
2. Cek permission folder: `chmod -R 755 storage/`
3. Pastikan file gambar ada di lokasi yang benar
4. Cek web server configuration untuk serving static files

### Path gambar salah?
Database seeder menyimpan path relatif: `hotels/hotel1.jpg`
Frontend perlu menambahkan base URL: `/storage/` + path dari database
