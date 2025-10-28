# Database Seeders Documentation

Dokumentasi lengkap untuk seeder Hotel Management System.

## 📋 Daftar Seeder

### 1. Master Data Seeders

#### CountrySeeder
- **File**: `CountrySeeder.php`
- **Deskripsi**: Membuat data negara
- **Data**: 10 negara (Indonesia, Malaysia, Singapore, Thailand, Philippines, Vietnam, Japan, South Korea, China, United States)
- **Urutan**: 1 (harus dijalankan pertama)

#### AmenitySeeder
- **File**: `AmenitySeeder.php`
- **Deskripsi**: Membuat data fasilitas hotel dan kamar
- **Data**: 
  - 10 amenities untuk hotel (WiFi, Swimming Pool, Gym, Restaurant, Spa, Bar, Parking, Airport Shuttle, Conference Room, 24/7 Reception)
  - 10 amenities untuk room (AC, TV, Mini Bar, Safe Box, Coffee Maker, Hair Dryer, Bathtub, Work Desk, Balcony, Room Service)
- **Urutan**: 2

---

### 2. User Seeders (By Role)

#### AdminSeeder
- **File**: `AdminSeeder.php`
- **Deskripsi**: Membuat user dengan role Admin
- **Data**: 5 admin users
- **Email Format**: `admin{1-5}@hotelmanagement.com`
- **Password**: `password123` (untuk semua user)
- **Urutan**: 3

#### CustomerSeeder
- **File**: `CustomerSeeder.php`
- **Deskripsi**: Membuat user customer (tamu hotel)
- **Data**: 30 customer users
- **Email Format**: `customer{1-30}@example.com`
- **Password**: `password123`
- **Urutan**: 4

#### OwnerSeeder
- **File**: `OwnerSeeder.php`
- **Deskripsi**: Membuat user dengan role Owner (pemilik hotel)
- **Data**: 10 owner users
- **Email Format**: `owner{1-10}@hotelowner.com`
- **Password**: `password123`
- **Note**: `hotel_id` akan di-assign di HotelSeeder
- **Urutan**: 5

---

### 3. Hotel Seeder

#### HotelSeeder
- **File**: `HotelSeeder.php`
- **Deskripsi**: Membuat hotel di berbagai negara dan assign owner
- **Data**: 
  - Indonesia: 5 hotels
  - Malaysia: 2 hotels
  - Singapore: 2 hotels
  - Thailand: 2 hotels
  - Philippines: 1 hotel
  - Vietnam: 1 hotel
  - Japan: 1 hotel
  - South Korea: 1 hotel
  - China: 1 hotel
  - United States: 1 hotel
  - **Total**: 17 hotels
- **Fitur**:
  - Assign owner ke setiap hotel
  - Assign 5-8 amenities per hotel
  - Create 3-5 images per hotel
  - Star rating: 3, 4, atau 5 bintang
- **Urutan**: 6

---

### 4. Receptionist Seeder

#### ReceptionistSeeder
- **File**: `ReceptionistSeeder.php`
- **Deskripsi**: Membuat user receptionist untuk setiap hotel
- **Data**: 1-2 receptionist per hotel (sekitar 17-34 receptionist total)
- **Email Format**: `receptionist{n}@{hotel_id}.hotel.com`
- **Password**: `password123`
- **Urutan**: 7 (setelah hotel dibuat)

---

### 5. Room Seeder

#### RoomSeeder
- **File**: `RoomSeeder.php`
- **Deskripsi**: Membuat kamar untuk setiap hotel
- **Data**: 10-15 rooms per hotel
- **Room Types**:
  - **Standard**: 2 capacity, 25 sqm, Queen Size bed, ~300K/night
  - **Deluxe**: 2 capacity, 32 sqm, King Size bed, ~500K/night
  - **Suite**: 3 capacity, 45 sqm, King Size bed, ~800K/night
  - **Family Suite**: 4 capacity, 55 sqm, 2 Queen Size beds, ~1.2M/night
  - **Presidential Suite**: 4 capacity, 80 sqm, King + Queen bed, ~2M/night
- **Fitur**:
  - Price multiplier berdasarkan star rating hotel (5★: 1.5x, 4★: 1.2x, 3★: 1.0x)
  - Assign 4-8 amenities per room
  - Create 2-4 images per room
  - Random status: available, occupied, maintenance
- **Urutan**: 8

---

### 6. Booking Seeder (Optional)

#### BookingSeeder
- **File**: `BookingSeeder.php`
- **Deskripsi**: Membuat sample booking data
- **Data**: 50 bookings
- **Fitur**:
  - Random customer, room, dates
  - Status: pending, confirmed, checked_in, checked_out, cancelled
  - Create payment untuk booking yang confirmed/checked_in/checked_out
  - Create invoice untuk booking dengan payment
- **Urutan**: 9 (optional, bisa di-comment)

---

## 🚀 Cara Menjalankan Seeder

### Menjalankan Semua Seeder

```bash
php artisan migrate:fresh --seed
```

### Menjalankan Seeder Tertentu

```bash
# Seeder individual
php artisan db:seed --class=CountrySeeder
php artisan db:seed --class=AmenitySeeder
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=CustomerSeeder
php artisan db:seed --class=OwnerSeeder
php artisan db:seed --class=HotelSeeder
php artisan db:seed --class=ReceptionistSeeder
php artisan db:seed --class=RoomSeeder
php artisan db:seed --class=BookingSeeder
```

### Menjalankan Seeder Tanpa Booking

Edit `DatabaseSeeder.php`, comment `BookingSeeder::class`:

```php
$this->call([
    // ...
    // BookingSeeder::class, // Commented out
]);
```

---

## 📊 Struktur Data yang Dihasilkan

### Summary

| Entity | Jumlah | Keterangan |
|--------|--------|------------|
| Countries | 10 | Negara di berbagai benua |
| Amenities | 20 | 10 hotel + 10 room amenities |
| Admins | 5 | Admin users |
| Customers | 30 | Regular users/tamu |
| Owners | 10 | Pemilik hotel |
| Hotels | 17 | Tersebar di 10 negara |
| Receptionists | 17-34 | 1-2 per hotel |
| Rooms | 170-255 | 10-15 per hotel |
| Bookings | 50 | Sample bookings (optional) |

---

## 🔑 Login Credentials

### Admin
```
Email: admin1@hotelmanagement.com (hingga admin5@hotelmanagement.com)
Password: password123
```

### Owner
```
Email: owner1@hotelowner.com (hingga owner10@hotelowner.com)
Password: password123
```

### Receptionist
```
Email: receptionist{n}@{hotel_id}.hotel.com
Password: password123
```

### Customer
```
Email: customer1@example.com (hingga customer30@example.com)
Password: password123
```

---

## 📝 Notes

1. **Urutan Penting**: Seeder harus dijalankan sesuai urutan di `DatabaseSeeder.php`
2. **Owner Assignment**: Owner di-assign ke hotel secara otomatis di HotelSeeder
3. **Hotel Distribution**: Hotel tersebar di 10 negara berbeda
4. **Room Pricing**: Harga kamar disesuaikan dengan star rating hotel
5. **Images**: Path gambar adalah placeholder, perlu diganti dengan real images
6. **Faker Data**: Data seperti nama, alamat, telepon menggunakan Faker

---

## 🔄 Reset Database

Untuk reset dan re-seed database:

```bash
php artisan migrate:fresh --seed
```

**Warning**: Perintah ini akan menghapus semua data dan membuat ulang database!

---

## 🛠️ Customization

### Menambah Jumlah Hotel per Negara

Edit `HotelSeeder.php`, tambahkan hotel di array `$hotelTemplates`:

```php
'Indonesia' => [
    'Grand Hotel Jakarta',
    'Bali Beach Resort',
    // Tambahkan hotel baru di sini
    'New Hotel Name',
],
```

### Menambah Negara Baru

1. Edit `CountrySeeder.php`, tambahkan negara baru
2. Edit `HotelSeeder.php`, tambahkan template hotel untuk negara tersebut

### Mengubah Jumlah User

Edit seeder yang sesuai dan ubah loop counter:

```php
// AdminSeeder.php
for ($i = 1; $i <= 10; $i++) { // Ubah dari 5 ke 10
    // ...
}
```

---

## 📧 Support

Jika ada pertanyaan atau issue, silakan hubungi tim development.

---

**Last Updated**: October 26, 2025
**Version**: 1.0.0
