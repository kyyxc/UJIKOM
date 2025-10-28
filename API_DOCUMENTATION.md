# 📚 Dokumentasi API - Hotel Management System

## Informasi Umum

**Base URL:** `http://your-domain.com/api`

**Format Response:** JSON

**Authentication:** Bearer Token (Laravel Sanctum)

---

## 🔐 Authentication Endpoints

### 1. Sign Up (Register)

Endpoint untuk mendaftarkan user baru.

**Endpoint:** `POST /api/auth/signup`

**Authentication:** Tidak diperlukan

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890"
}
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890",
      "role": "customer",
      "created_at": "2025-10-26T10:00:00.000000Z"
    },
    "token": "1|laravel_sanctum_token_here"
  }
}
```

**Response Error (422):**
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 2. Sign In (Login)

Endpoint untuk login user.

**Endpoint:** `POST /api/auth/signin`

**Authentication:** Tidak diperlukan

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "customer"
    },
    "token": "1|laravel_sanctum_token_here"
  }
}
```

**Response Error (401):**
```json
{
  "status": "error",
  "message": "Invalid credentials"
}
```

---

### 3. Sign Out (Logout)

Endpoint untuk logout user.

**Endpoint:** `POST /api/auth/signout`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

---

## 👤 User Profile Endpoints

Semua endpoint di bagian ini memerlukan authentication.

### 1. Get User Profile

Mendapatkan informasi profil user yang sedang login.

**Endpoint:** `GET /api/user/profile`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "profile_picture": "storage/profiles/user1.jpg",
    "role": "customer",
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 2. Update User Profile

Mengupdate informasi profil user.

**Endpoint:** `POST /api/user/profile`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
name: John Doe Updated
phone: 081234567890
profile_picture: [file] (optional)
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Doe Updated",
    "email": "john@example.com",
    "phone": "081234567890",
    "profile_picture": "storage/profiles/user1_updated.jpg"
  }
}
```

---

### 3. Delete Profile Picture

Menghapus foto profil user.

**Endpoint:** `DELETE /api/user/profile/picture`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Profile picture deleted successfully"
}
```

---

### 4. Change Password

Mengubah password user.

**Endpoint:** `POST /api/user/change-password`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "current_password": "oldpassword123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Password changed successfully"
}
```

**Response Error (400):**
```json
{
  "status": "error",
  "message": "Current password is incorrect"
}
```

---

## 🏨 Hotel Endpoints

### 1. Get All Hotels

Mendapatkan daftar semua hotel (accessible untuk public).

**Endpoint:** `GET /api/hotels`

**Authentication:** Tidak diperlukan

**Query Parameters:**
- `search` (optional): Cari berdasarkan nama hotel
- `country_id` (optional): Filter berdasarkan negara
- `min_price` (optional): Harga minimum
- `max_price` (optional): Harga maximum
- `per_page` (optional): Jumlah data per halaman (default: 15)

**Example Request:**
```
GET /api/hotels?search=Grand&country_id=1&min_price=100000&per_page=10
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Grand Hotel Jakarta",
        "description": "Hotel mewah di pusat kota Jakarta",
        "address": "Jl. Sudirman No. 1",
        "country": {
          "id": 1,
          "name": "Indonesia",
          "code": "ID"
        },
        "phone": "021-1234567",
        "email": "info@grandhotel.com",
        "star_rating": 5,
        "main_image": "storage/hotels/hotel1.jpg",
        "amenities": [
          {
            "id": 1,
            "name": "WiFi",
            "icon": "wifi"
          },
          {
            "id": 2,
            "name": "Swimming Pool",
            "icon": "pool"
          }
        ],
        "lowest_room_price": 500000,
        "available_rooms_count": 10,
        "created_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "per_page": 10,
    "total": 50,
    "last_page": 5
  }
}
```

---

### 2. Get Hotel Detail

Mendapatkan detail hotel berdasarkan ID.

**Endpoint:** `GET /api/hotels/{id}`

**Authentication:** Tidak diperlukan

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta",
    "description": "Hotel mewah di pusat kota Jakarta dengan fasilitas lengkap",
    "address": "Jl. Sudirman No. 1",
    "country": {
      "id": 1,
      "name": "Indonesia",
      "code": "ID"
    },
    "phone": "021-1234567",
    "email": "info@grandhotel.com",
    "website": "https://grandhotel.com",
    "star_rating": 5,
    "check_in_time": "14:00:00",
    "check_out_time": "12:00:00",
    "images": [
      {
        "id": 1,
        "image_url": "storage/hotels/hotel1_1.jpg",
        "is_primary": true
      },
      {
        "id": 2,
        "image_url": "storage/hotels/hotel1_2.jpg",
        "is_primary": false
      }
    ],
    "amenities": [
      {
        "id": 1,
        "name": "WiFi",
        "icon": "wifi",
        "description": "Free high-speed internet"
      },
      {
        "id": 2,
        "name": "Swimming Pool",
        "icon": "pool",
        "description": "Outdoor swimming pool"
      }
    ],
    "rooms": [
      {
        "id": 1,
        "room_number": "101",
        "type": "Deluxe",
        "capacity": 2,
        "price_per_night": 500000,
        "status": "available",
        "main_image": "storage/rooms/room1.jpg"
      }
    ],
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

**Response Error (404):**
```json
{
  "status": "error",
  "message": "Hotel not found"
}
```

---

## 🛏️ Room Endpoints

### 1. Get All Rooms

Mendapatkan daftar semua kamar.

**Endpoint:** `GET /api/rooms`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `hotel_id` (optional): Filter berdasarkan hotel
- `type` (optional): Filter berdasarkan tipe kamar (Standard, Deluxe, Suite, dll)
- `status` (optional): Filter berdasarkan status (available, occupied, maintenance)
- `min_price` (optional): Harga minimum per malam
- `max_price` (optional): Harga maximum per malam
- `min_capacity` (optional): Kapasitas minimum
- `per_page` (optional): Jumlah data per halaman

**Example Request:**
```
GET /api/rooms?hotel_id=1&status=available&min_capacity=2&per_page=10
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "hotel": {
          "id": 1,
          "name": "Grand Hotel Jakarta",
          "address": "Jl. Sudirman No. 1"
        },
        "room_number": "101",
        "type": "Deluxe",
        "capacity": 2,
        "price_per_night": 500000,
        "status": "available",
        "description": "Kamar deluxe dengan pemandangan kota",
        "size": "32 sqm",
        "bed_type": "King Size",
        "main_image": "storage/rooms/room1.jpg",
        "amenities": [
          {
            "id": 1,
            "name": "AC",
            "icon": "air_conditioner"
          },
          {
            "id": 2,
            "name": "TV",
            "icon": "tv"
          }
        ]
      }
    ],
    "per_page": 10,
    "total": 25,
    "last_page": 3
  }
}
```

---

### 2. Get Room Detail

Mendapatkan detail kamar berdasarkan ID.

**Endpoint:** `GET /api/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "address": "Jl. Sudirman No. 1",
      "phone": "021-1234567"
    },
    "room_number": "101",
    "type": "Deluxe",
    "capacity": 2,
    "price_per_night": 500000,
    "status": "available",
    "description": "Kamar deluxe dengan pemandangan kota dan fasilitas lengkap",
    "size": "32 sqm",
    "bed_type": "King Size",
    "images": [
      {
        "id": 1,
        "image_url": "storage/rooms/room1_1.jpg",
        "is_primary": true
      },
      {
        "id": 2,
        "image_url": "storage/rooms/room1_2.jpg",
        "is_primary": false
      }
    ],
    "amenities": [
      {
        "id": 1,
        "name": "AC",
        "icon": "air_conditioner",
        "description": "Air conditioning"
      },
      {
        "id": 2,
        "name": "TV",
        "icon": "tv",
        "description": "Smart TV 43 inch"
      }
    ],
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

**Response Error (404):**
```json
{
  "status": "error",
  "message": "Room not found"
}
```

---

## 📅 Booking Endpoints

Semua endpoint booking memerlukan authentication dengan role Customer.

### 1. Get All Bookings (User)

Mendapatkan semua booking milik user yang sedang login.

**Endpoint:** `GET /api/bookings`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): Filter berdasarkan status (pending, confirmed, checked_in, checked_out, cancelled)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "room": {
          "id": 1,
          "room_number": "101",
          "type": "Deluxe",
          "hotel": {
            "id": 1,
            "name": "Grand Hotel Jakarta"
          }
        },
        "check_in_date": "2025-11-01",
        "check_out_date": "2025-11-05",
        "number_of_guests": 2,
        "total_nights": 4,
        "price_per_night": 500000,
        "total_price": 2000000,
        "status": "confirmed",
        "special_requests": "Late check-in",
        "payment": {
          "id": 1,
          "amount": 2000000,
          "status": "paid",
          "payment_method": "qris",
          "transaction_date": "2025-10-26T10:00:00.000000Z"
        },
        "created_at": "2025-10-26T09:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 10,
    "last_page": 1
  }
}
```

---

### 2. Create Booking

Membuat booking baru.

**Endpoint:** `POST /api/bookings`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "room_id": 1,
  "check_in_date": "2025-11-01",
  "check_out_date": "2025-11-05",
  "number_of_guests": 2,
  "special_requests": "Late check-in please"
}
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Booking created successfully",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "room": {
      "id": 1,
      "room_number": "101",
      "type": "Deluxe",
      "hotel": {
        "id": 1,
        "name": "Grand Hotel Jakarta"
      }
    },
    "check_in_date": "2025-11-01",
    "check_out_date": "2025-11-05",
    "number_of_guests": 2,
    "total_nights": 4,
    "price_per_night": 500000,
    "total_price": 2000000,
    "status": "pending",
    "special_requests": "Late check-in please",
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

**Response Error (422):**
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "check_in_date": ["The check in date must be a date after today."],
    "room_id": ["The selected room is not available for the selected dates."]
  }
}
```

---

### 3. Get Booking Detail

Mendapatkan detail booking berdasarkan ID.

**Endpoint:** `GET /api/bookings/{id}`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890"
    },
    "room": {
      "id": 1,
      "room_number": "101",
      "type": "Deluxe",
      "capacity": 2,
      "price_per_night": 500000,
      "hotel": {
        "id": 1,
        "name": "Grand Hotel Jakarta",
        "address": "Jl. Sudirman No. 1",
        "phone": "021-1234567"
      }
    },
    "check_in_date": "2025-11-01",
    "check_out_date": "2025-11-05",
    "number_of_guests": 2,
    "total_nights": 4,
    "price_per_night": 500000,
    "total_price": 2000000,
    "status": "confirmed",
    "special_requests": "Late check-in please",
    "payment": {
      "id": 1,
      "amount": 2000000,
      "status": "paid",
      "payment_method": "qris",
      "transaction_date": "2025-10-26T10:00:00.000000Z",
      "midtrans_transaction_id": "MIDTRANS-123456"
    },
    "created_at": "2025-10-26T09:00:00.000000Z",
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

**Response Error (404):**
```json
{
  "status": "error",
  "message": "Booking not found"
}
```

---

### 4. Update Booking

Mengupdate booking (hanya untuk booking dengan status pending).

**Endpoint:** `PUT /api/bookings/{id}`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "check_in_date": "2025-11-02",
  "check_out_date": "2025-11-06",
  "number_of_guests": 3,
  "special_requests": "Updated special request"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Booking updated successfully",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "check_in_date": "2025-11-02",
    "check_out_date": "2025-11-06",
    "number_of_guests": 3,
    "total_nights": 4,
    "total_price": 2000000,
    "status": "pending",
    "special_requests": "Updated special request"
  }
}
```

---

### 5. Cancel Booking

Membatalkan booking.

**Endpoint:** `DELETE /api/bookings/{id}`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Booking cancelled successfully"
}
```

**Response Error (400):**
```json
{
  "status": "error",
  "message": "Cannot cancel booking that has been confirmed or completed"
}
```

---

## 💳 Payment Endpoints

### 1. Create Payment

Membuat pembayaran untuk booking.

**Endpoint:** `POST /api/bookings/{id}/pay`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "payment_method": "qris"
}
```

**Note:** Payment methods yang tersedia: `credit_card`, `bank_transfer`, `qris`, `gopay`, `shopeepay`

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Payment initiated successfully",
  "data": {
    "payment_id": 1,
    "booking_id": 1,
    "amount": 2000000,
    "payment_method": "qris",
    "status": "pending",
    "midtrans_snap_token": "snap_token_here",
    "midtrans_redirect_url": "https://app.midtrans.com/snap/v2/vtweb/snap_token_here"
  }
}
```

---

### 2. Payment Callback

Endpoint untuk menerima callback dari payment gateway (Midtrans).

**Endpoint:** `POST /api/payments/callback`

**Authentication:** Tidak diperlukan (diakses oleh Midtrans)

**Request Body:** (Dikirim oleh Midtrans)
```json
{
  "transaction_status": "settlement",
  "order_id": "BK-20251026-001",
  "gross_amount": "2000000.00",
  "payment_type": "qris",
  "transaction_id": "MIDTRANS-123456",
  "transaction_time": "2025-10-26 10:00:00"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Payment callback processed"
}
```

---

### 3. Test Payment Success (Development Only)

Endpoint untuk testing pembayaran tanpa Midtrans (hanya untuk development).

**Endpoint:** `POST /api/payments/test-success/{payment_id}`

**Authentication:** Tidak diperlukan

**Response Success (200):**
```json
{
  "message": "Payment forced to success",
  "payment": {
    "id": 1,
    "status": "paid",
    "midtrans_transaction_id": "TEST-abc123",
    "booking": {
      "id": 1,
      "status": "confirmed",
      "room": {
        "id": 1,
        "status": "occupied"
      }
    }
  }
}
```

---

## 🧾 Invoice Endpoints

### 1. Get All Invoices

Mendapatkan semua invoice milik user.

**Endpoint:** `GET /api/invoices`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "invoice_number": "INV-20251026-001",
        "booking": {
          "id": 1,
          "booking_code": "BK-20251026-001",
          "room": {
            "id": 1,
            "room_number": "101",
            "type": "Deluxe"
          },
          "hotel": {
            "id": 1,
            "name": "Grand Hotel Jakarta"
          }
        },
        "payment": {
          "id": 1,
          "amount": 2000000,
          "payment_method": "qris",
          "status": "paid"
        },
        "subtotal": 2000000,
        "tax": 200000,
        "total_amount": 2200000,
        "issued_date": "2025-10-26",
        "created_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 5,
    "last_page": 1
  }
}
```

---

### 2. Get Invoice Detail

Mendapatkan detail invoice berdasarkan ID.

**Endpoint:** `GET /api/invoices/{id}`

**Authentication:** Bearer Token (Required) - Role: Customer

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "invoice_number": "INV-20251026-001",
    "booking": {
      "id": 1,
      "booking_code": "BK-20251026-001",
      "check_in_date": "2025-11-01",
      "check_out_date": "2025-11-05",
      "total_nights": 4,
      "number_of_guests": 2,
      "room": {
        "id": 1,
        "room_number": "101",
        "type": "Deluxe"
      },
      "hotel": {
        "id": 1,
        "name": "Grand Hotel Jakarta",
        "address": "Jl. Sudirman No. 1",
        "phone": "021-1234567"
      }
    },
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890"
    },
    "payment": {
      "id": 1,
      "amount": 2000000,
      "payment_method": "qris",
      "status": "paid",
      "transaction_date": "2025-10-26T10:00:00.000000Z"
    },
    "subtotal": 2000000,
    "tax": 200000,
    "service_charge": 0,
    "total_amount": 2200000,
    "issued_date": "2025-10-26",
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

## 🎯 Amenity Endpoints

### 1. Get All Amenities

Mendapatkan semua amenity yang tersedia (untuk hotel dan room).

**Endpoint:** `GET /api/amenities`

**Authentication:** Tidak diperlukan

**Query Parameters:**
- `type` (optional): Filter berdasarkan tipe (hotel, room)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "WiFi",
        "icon": "wifi",
        "description": "Free high-speed internet",
        "type": "both"
      },
      {
        "id": 2,
        "name": "Swimming Pool",
        "icon": "pool",
        "description": "Outdoor swimming pool",
        "type": "hotel"
      },
      {
        "id": 3,
        "name": "Air Conditioner",
        "icon": "ac",
        "description": "Air conditioning in room",
        "type": "room"
      }
    ],
    "per_page": 15,
    "total": 20,
    "last_page": 2
  }
}
```

---

## 📋 Status Codes

| Status Code | Deskripsi |
|------------|-----------|
| 200 | OK - Request berhasil |
| 201 | Created - Resource berhasil dibuat |
| 400 | Bad Request - Request tidak valid |
| 401 | Unauthorized - Authentication gagal |
| 403 | Forbidden - Tidak memiliki akses |
| 404 | Not Found - Resource tidak ditemukan |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Error di server |

---

## 🔒 Authentication

Semua endpoint yang memerlukan authentication menggunakan **Bearer Token** dari Laravel Sanctum.

**Cara menggunakan:**
1. Login melalui endpoint `/api/auth/signin`
2. Dapatkan token dari response
3. Sertakan token di header setiap request:
   ```
   Authorization: Bearer {your_token_here}
   ```

---

## 📝 Notes

1. **Pagination**: Semua endpoint yang mengembalikan list menggunakan pagination dengan default 15 items per halaman
2. **Timestamps**: Semua timestamp menggunakan format ISO 8601 (RFC 3339)
3. **Currency**: Semua harga dalam Rupiah (IDR)
4. **Date Format**: Format tanggal menggunakan `YYYY-MM-DD`
5. **Time Format**: Format waktu menggunakan `HH:MM:SS`

---

## 🔄 Booking Status Flow

```
pending → confirmed → checked_in → checked_out
   ↓
cancelled
```

**Status Explanation:**
- `pending`: Booking dibuat, menunggu pembayaran
- `confirmed`: Pembayaran berhasil, booking dikonfirmasi
- `checked_in`: Tamu sudah check-in
- `checked_out`: Tamu sudah check-out
- `cancelled`: Booking dibatalkan

---

## 💰 Payment Status Flow

```
pending → paid
   ↓
failed/expired
```

**Status Explanation:**
- `pending`: Menunggu pembayaran
- `paid`: Pembayaran berhasil
- `failed`: Pembayaran gagal
- `expired`: Pembayaran kadaluarsa

---

# 👨‍💼 ADMIN ENDPOINTS

Semua endpoint admin memerlukan authentication dengan role **Admin** dan menggunakan prefix `/api/admin`.

---

## 🏨 Admin - Hotel Management

### 1. Get All Hotels (Admin)

Mendapatkan semua hotel untuk keperluan management.

**Endpoint:** `GET /api/admin/hotels`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `search` (optional): Cari berdasarkan nama hotel
- `country_id` (optional): Filter berdasarkan negara
- `status` (optional): Filter berdasarkan status (active, inactive)
- `owner_id` (optional): Filter berdasarkan owner
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Grand Hotel Jakarta",
        "description": "Hotel mewah di pusat kota Jakarta",
        "address": "Jl. Sudirman No. 1",
        "country": {
          "id": 1,
          "name": "Indonesia"
        },
        "owner": {
          "id": 1,
          "name": "Owner Name",
          "email": "owner@example.com"
        },
        "phone": "021-1234567",
        "email": "info@grandhotel.com",
        "star_rating": 5,
        "status": "active",
        "total_rooms": 50,
        "available_rooms": 30,
        "created_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

---

### 2. Get Hotel Detail (Admin)

Mendapatkan detail hotel untuk keperluan management.

**Endpoint:** `GET /api/admin/hotels/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta",
    "description": "Hotel mewah di pusat kota Jakarta",
    "address": "Jl. Sudirman No. 1",
    "country": {
      "id": 1,
      "name": "Indonesia",
      "code": "ID"
    },
    "owner": {
      "id": 1,
      "name": "Owner Name",
      "email": "owner@example.com",
      "phone": "081234567890"
    },
    "phone": "021-1234567",
    "email": "info@grandhotel.com",
    "website": "https://grandhotel.com",
    "star_rating": 5,
    "status": "active",
    "check_in_time": "14:00:00",
    "check_out_time": "12:00:00",
    "images": [
      {
        "id": 1,
        "image_url": "storage/hotels/hotel1_1.jpg",
        "is_primary": true
      }
    ],
    "amenities": [
      {
        "id": 1,
        "name": "WiFi",
        "icon": "wifi"
      }
    ],
    "total_rooms": 50,
    "available_rooms": 30,
    "occupied_rooms": 15,
    "maintenance_rooms": 5,
    "total_bookings": 120,
    "total_revenue": 50000000,
    "created_at": "2025-10-26T10:00:00.000000Z",
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

**Response Error (404):**
```json
{
  "status": "error",
  "message": "Hotel not found"
}
```

---

### 3. Create Hotel (Admin)

Membuat hotel baru.

**Endpoint:** `POST /api/admin/hotels`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
name: Grand Hotel Jakarta
description: Hotel mewah di pusat kota Jakarta
address: Jl. Sudirman No. 1
country_id: 1
owner_id: 1
phone: 021-1234567
email: info@grandhotel.com
website: https://grandhotel.com (optional)
star_rating: 5
check_in_time: 14:00
check_out_time: 12:00
status: active
amenity_ids: [1,2,3] (JSON array)
images[]: [file] (multiple images)
primary_image_index: 0
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Hotel created successfully",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta",
    "description": "Hotel mewah di pusat kota Jakarta",
    "address": "Jl. Sudirman No. 1",
    "phone": "021-1234567",
    "email": "info@grandhotel.com",
    "star_rating": 5,
    "status": "active",
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 4. Update Hotel (Admin)

Mengupdate informasi hotel.

**Endpoint:** `PUT /api/admin/hotels/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Grand Hotel Jakarta Updated",
  "description": "Updated description",
  "address": "Jl. Sudirman No. 2",
  "country_id": 1,
  "owner_id": 1,
  "phone": "021-1234567",
  "email": "info@grandhotel.com",
  "website": "https://grandhotel.com",
  "star_rating": 5,
  "check_in_time": "14:00",
  "check_out_time": "12:00",
  "status": "active"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Hotel updated successfully",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta Updated",
    "description": "Updated description",
    "status": "active",
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

### 5. Delete Hotel (Admin)

Menghapus hotel (soft delete).

**Endpoint:** `DELETE /api/admin/hotels/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Hotel deleted successfully"
}
```

**Response Error (400):**
```json
{
  "status": "error",
  "message": "Cannot delete hotel with active bookings"
}
```

---

## 🛏️ Admin - Room Management

### 1. Get All Rooms (Admin)

Mendapatkan semua kamar untuk keperluan management.

**Endpoint:** `GET /api/admin/rooms`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `hotel_id` (optional): Filter berdasarkan hotel
- `type` (optional): Filter berdasarkan tipe kamar
- `status` (optional): Filter berdasarkan status (available, occupied, maintenance)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "hotel": {
          "id": 1,
          "name": "Grand Hotel Jakarta"
        },
        "room_number": "101",
        "type": "Deluxe",
        "capacity": 2,
        "price_per_night": 500000,
        "status": "available",
        "size": "32 sqm",
        "bed_type": "King Size",
        "total_bookings": 50,
        "created_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 200,
    "last_page": 14
  }
}
```

---

### 2. Get Room Detail (Admin)

Mendapatkan detail kamar untuk keperluan management.

**Endpoint:** `GET /api/admin/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "address": "Jl. Sudirman No. 1"
    },
    "room_number": "101",
    "type": "Deluxe",
    "capacity": 2,
    "price_per_night": 500000,
    "status": "available",
    "description": "Kamar deluxe dengan pemandangan kota",
    "size": "32 sqm",
    "bed_type": "King Size",
    "images": [
      {
        "id": 1,
        "image_url": "storage/rooms/room1_1.jpg",
        "is_primary": true
      }
    ],
    "amenities": [
      {
        "id": 1,
        "name": "AC",
        "icon": "air_conditioner"
      }
    ],
    "total_bookings": 50,
    "total_revenue": 25000000,
    "average_occupancy_rate": 75,
    "created_at": "2025-10-26T10:00:00.000000Z",
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 3. Create Room (Admin)

Membuat kamar baru.

**Endpoint:** `POST /api/admin/rooms`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
hotel_id: 1
room_number: 101
type: Deluxe
capacity: 2
price_per_night: 500000
status: available
description: Kamar deluxe dengan pemandangan kota
size: 32 sqm
bed_type: King Size
amenity_ids: [1,2,3] (JSON array)
images[]: [file] (multiple images)
primary_image_index: 0
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Room created successfully",
  "data": {
    "id": 1,
    "hotel_id": 1,
    "room_number": "101",
    "type": "Deluxe",
    "capacity": 2,
    "price_per_night": 500000,
    "status": "available",
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 4. Update Room (Admin)

Mengupdate informasi kamar.

**Endpoint:** `PUT /api/admin/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "room_number": "101A",
  "type": "Deluxe",
  "capacity": 2,
  "price_per_night": 550000,
  "status": "available",
  "description": "Updated description",
  "size": "35 sqm",
  "bed_type": "King Size"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Room updated successfully",
  "data": {
    "id": 1,
    "room_number": "101A",
    "price_per_night": 550000,
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

### 5. Delete Room (Admin)

Menghapus kamar (soft delete).

**Endpoint:** `DELETE /api/admin/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Room deleted successfully"
}
```

**Response Error (400):**
```json
{
  "status": "error",
  "message": "Cannot delete room with active bookings"
}
```

---

## 📊 Admin - Dashboard

### 1. Get Dashboard Stats

Mendapatkan statistik dashboard keseluruhan.

**Endpoint:** `GET /api/admin/dashboard/stats`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_hotels": 50,
    "total_rooms": 500,
    "total_bookings": 1200,
    "total_revenue": 500000000,
    "total_users": 5000,
    "total_owners": 50,
    "total_receptionists": 100,
    "active_bookings": 150,
    "pending_owner_registrations": 10,
    "occupancy_rate": 75.5,
    "revenue_this_month": 50000000,
    "revenue_last_month": 45000000,
    "growth_percentage": 11.11
  }
}
```

---

### 2. Get Reservations & Revenue Chart

Mendapatkan data chart untuk reservasi dan revenue.

**Endpoint:** `GET /api/admin/dashboard/chart-data`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` (optional): Period untuk chart (week, month, year) - default: month

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "labels": ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    "reservations": [120, 150, 180, 160, 200, 220],
    "revenue": [50000000, 60000000, 75000000, 70000000, 85000000, 95000000]
  }
}
```

---

### 3. Get Recent Reservations

Mendapatkan daftar reservasi terbaru.

**Endpoint:** `GET /api/admin/dashboard/recent-reservations`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `limit` (optional): Jumlah data yang ditampilkan (default: 10)

**Response Success (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "booking_code": "BK-20251026-001",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "hotel": {
        "id": 1,
        "name": "Grand Hotel Jakarta"
      },
      "room": {
        "id": 1,
        "room_number": "101",
        "type": "Deluxe"
      },
      "check_in_date": "2025-11-01",
      "check_out_date": "2025-11-05",
      "total_price": 2000000,
      "status": "confirmed",
      "created_at": "2025-10-26T10:00:00.000000Z"
    }
  ]
}
```

---

### 4. Get Hotel Performance

Mendapatkan performa masing-masing hotel.

**Endpoint:** `GET /api/admin/dashboard/hotel-performance`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `limit` (optional): Jumlah hotel yang ditampilkan (default: 10)
- `sort` (optional): Sort by (revenue, bookings, occupancy) - default: revenue

**Response Success (200):**
```json
{
  "status": "success",
  "data": [
    {
      "hotel_id": 1,
      "hotel_name": "Grand Hotel Jakarta",
      "total_bookings": 150,
      "total_revenue": 75000000,
      "occupancy_rate": 85.5,
      "average_rating": 4.8,
      "total_rooms": 50
    },
    {
      "hotel_id": 2,
      "hotel_name": "Royal Hotel Bali",
      "total_bookings": 120,
      "total_revenue": 60000000,
      "occupancy_rate": 75.0,
      "average_rating": 4.5,
      "total_rooms": 40
    }
  ]
}
```

---

### 5. Get Booking Status Distribution

Mendapatkan distribusi status booking.

**Endpoint:** `GET /api/admin/dashboard/status-distribution`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "pending": 50,
    "confirmed": 300,
    "checked_in": 100,
    "checked_out": 700,
    "cancelled": 50
  }
}
```

---

### 6. Get Payment Methods Summary

Mendapatkan ringkasan metode pembayaran.

**Endpoint:** `GET /api/admin/dashboard/payment-methods`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "qris": {
      "count": 500,
      "total_amount": 250000000,
      "percentage": 40
    },
    "credit_card": {
      "count": 400,
      "total_amount": 200000000,
      "percentage": 32
    },
    "bank_transfer": {
      "count": 300,
      "total_amount": 150000000,
      "percentage": 24
    },
    "gopay": {
      "count": 50,
      "total_amount": 25000000,
      "percentage": 4
    }
  }
}
```

---

### 7. Get Quick Stats

Mendapatkan statistik cepat untuk dashboard.

**Endpoint:** `GET /api/admin/dashboard/quick-stats`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "today_check_ins": 25,
    "today_check_outs": 20,
    "today_bookings": 15,
    "today_revenue": 7500000,
    "available_rooms": 200,
    "occupied_rooms": 250,
    "maintenance_rooms": 50
  }
}
```

---

### 8. Get All Dashboard Data

Mendapatkan semua data dashboard sekaligus (kombinasi semua endpoint dashboard).

**Endpoint:** `GET /api/admin/dashboard/all`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "stats": { "..." },
    "chart_data": { "..." },
    "recent_reservations": [ "..." ],
    "hotel_performance": [ "..." ],
    "status_distribution": { "..." },
    "payment_methods": { "..." },
    "quick_stats": { "..." }
  }
}
```

---

## 📅 Admin - Booking Management

### 1. Get All Bookings (Admin)

Mendapatkan semua booking untuk management.

**Endpoint:** `GET /api/admin/bookings`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): Filter berdasarkan status
- `hotel_id` (optional): Filter berdasarkan hotel
- `user_id` (optional): Filter berdasarkan user
- `date_from` (optional): Filter dari tanggal
- `date_to` (optional): Filter sampai tanggal
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "hotel": {
          "id": 1,
          "name": "Grand Hotel Jakarta"
        },
        "room": {
          "id": 1,
          "room_number": "101",
          "type": "Deluxe"
        },
        "check_in_date": "2025-11-01",
        "check_out_date": "2025-11-05",
        "total_price": 2000000,
        "status": "confirmed",
        "payment_status": "paid",
        "created_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 500,
    "last_page": 34
  }
}
```

---

### 2. Get Booking Detail (Admin)

Mendapatkan detail booking untuk management.

**Endpoint:** `GET /api/admin/bookings/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890"
    },
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "address": "Jl. Sudirman No. 1",
      "phone": "021-1234567"
    },
    "room": {
      "id": 1,
      "room_number": "101",
      "type": "Deluxe",
      "capacity": 2
    },
    "check_in_date": "2025-11-01",
    "check_out_date": "2025-11-05",
    "number_of_guests": 2,
    "total_nights": 4,
    "price_per_night": 500000,
    "total_price": 2000000,
    "status": "confirmed",
    "special_requests": "Late check-in please",
    "payment": {
      "id": 1,
      "amount": 2000000,
      "status": "paid",
      "payment_method": "qris",
      "transaction_date": "2025-10-26T10:00:00.000000Z"
    },
    "created_at": "2025-10-26T09:00:00.000000Z",
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 3. Update Booking (Admin)

Mengupdate booking (admin dapat mengupdate booking apapun).

**Endpoint:** `PUT /api/admin/bookings/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "room_id": 1,
  "check_in_date": "2025-11-02",
  "check_out_date": "2025-11-06",
  "number_of_guests": 3,
  "status": "confirmed",
  "special_requests": "Updated by admin"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Booking updated successfully",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "status": "confirmed",
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

### 4. Delete/Cancel Booking (Admin)

Membatalkan atau menghapus booking.

**Endpoint:** `DELETE /api/admin/bookings/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Booking cancelled successfully"
}
```

---

## 👥 Admin - User Management

### 1. Get All Users

Mendapatkan semua user untuk management.

**Endpoint:** `GET /api/admin/users`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `role` (optional): Filter berdasarkan role (customer, owner, receptionist, admin)
- `search` (optional): Cari berdasarkan nama atau email
- `status` (optional): Filter berdasarkan status (active, inactive, suspended)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "081234567890",
        "role": "customer",
        "status": "active",
        "total_bookings": 10,
        "total_spent": 10000000,
        "created_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 5000,
    "last_page": 334
  }
}
```

---

### 2. Get User Detail

Mendapatkan detail user.

**Endpoint:** `GET /api/admin/users/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "profile_picture": "storage/profiles/user1.jpg",
    "role": "customer",
    "status": "active",
    "email_verified_at": "2025-10-26T10:00:00.000000Z",
    "total_bookings": 10,
    "total_spent": 10000000,
    "recent_bookings": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "hotel_name": "Grand Hotel Jakarta",
        "total_price": 2000000,
        "status": "confirmed",
        "created_at": "2025-10-26T09:00:00.000000Z"
      }
    ],
    "created_at": "2025-10-26T10:00:00.000000Z",
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 3. Create User

Membuat user baru (admin dapat membuat user dengan role apapun).

**Endpoint:** `POST /api/admin/users`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890",
  "role": "customer",
  "status": "active"
}
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "User created successfully",
  "data": {
    "id": 100,
    "name": "New User",
    "email": "newuser@example.com",
    "phone": "081234567890",
    "role": "customer",
    "status": "active",
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 4. Update User

Mengupdate informasi user.

**Endpoint:** `PUT /api/admin/users/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Updated Name",
  "email": "updated@example.com",
  "phone": "081234567890",
  "role": "customer",
  "status": "active"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "User updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Name",
    "email": "updated@example.com",
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

### 5. Delete User

Menghapus user (soft delete).

**Endpoint:** `DELETE /api/admin/users/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "User deleted successfully"
}
```

---

### 6. Get User Statistics

Mendapatkan statistik user.

**Endpoint:** `GET /api/admin/users/statistics`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_users": 5000,
    "total_customers": 4500,
    "total_owners": 50,
    "total_receptionists": 100,
    "total_admins": 5,
    "active_users": 4800,
    "inactive_users": 150,
    "suspended_users": 50,
    "new_users_this_month": 250,
    "new_users_last_month": 200
  }
}
```

---

### 7. Bulk User Actions

Melakukan aksi bulk terhadap multiple users.

**Endpoint:** `POST /api/admin/users/bulk-action`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "user_ids": [1, 2, 3, 4, 5],
  "action": "activate",
  "reason": "Bulk activation"
}
```

**Available Actions:**
- `activate`: Mengaktifkan users
- `deactivate`: Menonaktifkan users
- `suspend`: Suspend users
- `delete`: Menghapus users

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Bulk action completed successfully",
  "data": {
    "success_count": 5,
    "failed_count": 0,
    "action": "activate"
  }
}
```

---

## 🏢 Admin - Owner Registration Management

### 1. Get All Owner Registrations

Mendapatkan semua pendaftaran owner.

**Endpoint:** `GET /api/admin/owner-registrations`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): Filter berdasarkan status (pending, approved, rejected)
- `search` (optional): Cari berdasarkan nama atau email
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "owner": {
          "id": 10,
          "name": "Owner Name",
          "email": "owner@example.com",
          "phone": "081234567890"
        },
        "hotel": {
          "id": 1,
          "name": "Grand Hotel Jakarta",
          "address": "Jl. Sudirman No. 1"
        },
        "status": "pending",
        "registration_step": 5,
        "submitted_at": "2025-10-26T10:00:00.000000Z",
        "documents_complete": true,
        "waiting_days": 2
      }
    ],
    "per_page": 15,
    "total": 50,
    "last_page": 4
  }
}
```

---

### 2. Get Registration Detail

Mendapatkan detail pendaftaran owner.

**Endpoint:** `GET /api/admin/owner-registrations/{id}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "owner": {
      "id": 10,
      "name": "Owner Name",
      "email": "owner@example.com",
      "phone": "081234567890",
      "created_at": "2025-10-20T10:00:00.000000Z"
    },
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "description": "Hotel mewah di pusat kota Jakarta",
      "address": "Jl. Sudirman No. 1",
      "country_id": 1,
      "phone": "021-1234567",
      "email": "info@grandhotel.com",
      "star_rating": 5,
      "images_count": 5,
      "amenities_count": 10
    },
    "banking_info": {
      "bank_name": "BCA",
      "account_number": "1234567890",
      "account_holder": "Owner Name"
    },
    "documents": {
      "id_card": "storage/documents/id_card.pdf",
      "business_license": "storage/documents/business_license.pdf",
      "tax_id": "storage/documents/tax_id.pdf",
      "ownership_proof": "storage/documents/ownership_proof.pdf"
    },
    "status": "pending",
    "registration_step": 5,
    "submitted_at": "2025-10-26T10:00:00.000000Z",
    "reviewed_at": null,
    "reviewed_by": null,
    "admin_notes": null
  }
}
```

---

### 3. Approve Owner Registration

Menyetujui pendaftaran owner.

**Endpoint:** `POST /api/admin/owner-registrations/{id}/approve`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "notes": "Registration approved. All documents verified."
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Owner registration approved successfully",
  "data": {
    "id": 1,
    "status": "approved",
    "reviewed_at": "2025-10-26T11:00:00.000000Z",
    "reviewed_by": {
      "id": 1,
      "name": "Admin Name"
    },
    "admin_notes": "Registration approved. All documents verified."
  }
}
```

---

### 4. Reject Owner Registration

Menolak pendaftaran owner.

**Endpoint:** `POST /api/admin/owner-registrations/{id}/reject`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "reason": "Incomplete documents. Business license is not valid.",
  "notes": "Please resubmit with valid business license."
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Owner registration rejected",
  "data": {
    "id": 1,
    "status": "rejected",
    "reviewed_at": "2025-10-26T11:00:00.000000Z",
    "reviewed_by": {
      "id": 1,
      "name": "Admin Name"
    },
    "rejection_reason": "Incomplete documents. Business license is not valid.",
    "admin_notes": "Please resubmit with valid business license."
  }
}
```

---

### 5. Get Registration Statistics

Mendapatkan statistik pendaftaran owner.

**Endpoint:** `GET /api/admin/owner-registrations/statistics`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_registrations": 100,
    "pending_registrations": 10,
    "approved_registrations": 80,
    "rejected_registrations": 10,
    "pending_over_7_days": 3,
    "approved_this_month": 15,
    "rejected_this_month": 2,
    "average_processing_days": 3.5
  }
}
```

---

### 6. Get Waiting Analysis

Mendapatkan analisis registrasi yang menunggu approval.

**Endpoint:** `GET /api/admin/owner-registrations/waiting-analysis`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "waiting_0_3_days": 5,
    "waiting_4_7_days": 3,
    "waiting_over_7_days": 2,
    "oldest_waiting": {
      "id": 1,
      "owner_name": "Owner Name",
      "hotel_name": "Grand Hotel Jakarta",
      "waiting_days": 15,
      "submitted_at": "2025-10-11T10:00:00.000000Z"
    }
  }
}
```

---

### 7. Get Recent Registrations

Mendapatkan pendaftaran terbaru.

**Endpoint:** `GET /api/admin/owner-registrations/recent`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `limit` (optional): Jumlah data (default: 10)

**Response Success (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "owner_name": "Owner Name",
      "owner_email": "owner@example.com",
      "hotel_name": "Grand Hotel Jakarta",
      "status": "pending",
      "submitted_at": "2025-10-26T10:00:00.000000Z",
      "waiting_days": 0
    }
  ]
}
```

---

### 8. Search Registrations

Mencari registrasi berdasarkan keyword.

**Endpoint:** `GET /api/admin/owner-registrations/search`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `q` (required): Keyword pencarian

**Response Success (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "owner_name": "Owner Name",
      "owner_email": "owner@example.com",
      "hotel_name": "Grand Hotel Jakarta",
      "status": "pending",
      "submitted_at": "2025-10-26T10:00:00.000000Z"
    }
  ]
}
```

---

### 9. Get Registration History

Mendapatkan riwayat review registrasi.

**Endpoint:** `GET /api/admin/owner-registrations/history`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): Filter by status (approved, rejected)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "owner_name": "Owner Name",
        "hotel_name": "Grand Hotel Jakarta",
        "status": "approved",
        "submitted_at": "2025-10-20T10:00:00.000000Z",
        "reviewed_at": "2025-10-22T10:00:00.000000Z",
        "reviewed_by": "Admin Name",
        "processing_days": 2
      }
    ],
    "per_page": 15,
    "total": 90,
    "last_page": 6
  }
}
```

---

### 10. Bulk Approve Registrations

Menyetujui multiple registrasi sekaligus.

**Endpoint:** `POST /api/admin/owner-registrations/bulk-approve`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "registration_ids": [1, 2, 3, 4, 5],
  "notes": "Bulk approval for verified registrations"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Bulk approval completed",
  "data": {
    "success_count": 5,
    "failed_count": 0
  }
}
```

---

### 11. Bulk Reject Registrations

Menolak multiple registrasi sekaligus.

**Endpoint:** `POST /api/admin/owner-registrations/bulk-reject`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "registration_ids": [6, 7, 8],
  "reason": "Incomplete documentation",
  "notes": "Please complete all required documents"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Bulk rejection completed",
  "data": {
    "success_count": 3,
    "failed_count": 0
  }
}
```

---

### 12. Download Registration Document

Mendownload dokumen registrasi.

**Endpoint:** `GET /api/admin/owner-registrations/{id}/download/{document_type}`

**Authentication:** Bearer Token (Required) - Role: Admin

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**
- `document_type`: Tipe dokumen (id_card, business_license, tax_id, ownership_proof)

**Response Success (200):**
```
File download (PDF/Image)
```

---

# 🏢 OWNER ENDPOINTS

Semua endpoint owner memerlukan authentication dengan token yang valid dan menggunakan prefix `/api`.

---

## 📝 Owner Registration Endpoints

### 1. Owner Registration - Step 1 (Create Account)

Membuat akun owner baru (step pertama pendaftaran).

**Endpoint:** `POST /api/register/step-1`

**Authentication:** Tidak diperlukan

**Request Body:**
```json
{
  "name": "Owner Name",
  "email": "owner@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890"
}
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Account created successfully. Please proceed to step 2.",
  "data": {
    "user": {
      "id": 10,
      "name": "Owner Name",
      "email": "owner@example.com",
      "phone": "081234567890",
      "role": "owner",
      "created_at": "2025-10-26T10:00:00.000000Z"
    },
    "token": "10|laravel_sanctum_token_here",
    "next_step": 2,
    "registration_status": "step_1_completed"
  }
}
```

**Response Error (422):**
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 2. Owner Registration - Step 2 (Hotel Basic Info)

Mengisi informasi dasar hotel.

**Endpoint:** `POST /api/register/step-2`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "hotel_name": "Grand Hotel Jakarta",
  "hotel_description": "Hotel mewah di pusat kota Jakarta dengan fasilitas lengkap",
  "address": "Jl. Sudirman No. 1",
  "country_id": 1,
  "phone": "021-1234567",
  "email": "info@grandhotel.com",
  "website": "https://grandhotel.com",
  "star_rating": 5,
  "check_in_time": "14:00",
  "check_out_time": "12:00"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Hotel basic information saved. Please proceed to step 3.",
  "data": {
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "address": "Jl. Sudirman No. 1",
      "phone": "021-1234567",
      "email": "info@grandhotel.com",
      "star_rating": 5
    },
    "next_step": 3,
    "registration_status": "step_2_completed"
  }
}
```

---

### 3. Owner Registration - Step 3 (Amenities & Images)

Menambahkan fasilitas dan gambar hotel.

**Endpoint:** `POST /api/register/step-3`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
amenity_ids: [1,2,3,4,5] (JSON array)
images[]: [file] (multiple images, min: 3, max: 10)
primary_image_index: 0 (index gambar yang dijadikan primary)
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Amenities and images uploaded successfully. Please proceed to step 4.",
  "data": {
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "amenities_count": 5,
      "images_count": 5,
      "primary_image": "storage/hotels/hotel1_primary.jpg"
    },
    "next_step": 4,
    "registration_status": "step_3_completed"
  }
}
```

---

### 4. Owner Registration - Step 4 (Banking & Documents)

Mengisi informasi banking dan upload dokumen.

**Endpoint:** `POST /api/register/step-4`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
bank_name: BCA
account_number: 1234567890
account_holder: Owner Name
id_card: [file] (PDF or Image)
business_license: [file] (PDF or Image)
tax_id: [file] (PDF or Image)
ownership_proof: [file] (PDF or Image)
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Banking information and documents uploaded successfully. Please proceed to step 5.",
  "data": {
    "banking_info": {
      "bank_name": "BCA",
      "account_number": "1234567890",
      "account_holder": "Owner Name"
    },
    "documents": {
      "id_card": "uploaded",
      "business_license": "uploaded",
      "tax_id": "uploaded",
      "ownership_proof": "uploaded"
    },
    "next_step": 5,
    "registration_status": "step_4_completed"
  }
}
```

---

### 5. Owner Registration - Step 5 (Confirm & Submit)

Konfirmasi dan submit pendaftaran untuk review admin.

**Endpoint:** `POST /api/register/step-5`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "agree_to_terms": true,
  "confirm_data_accuracy": true
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Registration submitted successfully. Your application will be reviewed by admin.",
  "data": {
    "registration_id": 1,
    "status": "pending_review",
    "submitted_at": "2025-10-26T10:00:00.000000Z",
    "estimated_review_time": "3-5 business days"
  }
}
```

---

### 6. Get Registration Status

Mengecek status pendaftaran owner.

**Endpoint:** `GET /api/register/status`

**Authentication:** Bearer Token (Required)

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "registration_id": 1,
    "current_step": 5,
    "status": "pending_review",
    "submitted_at": "2025-10-26T10:00:00.000000Z",
    "reviewed_at": null,
    "can_edit": false,
    "steps_completed": {
      "step_1": true,
      "step_2": true,
      "step_3": true,
      "step_4": true,
      "step_5": true
    },
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "status": "pending"
    },
    "admin_feedback": null
  }
}
```

**Response if Approved:**
```json
{
  "status": "success",
  "data": {
    "registration_id": 1,
    "status": "approved",
    "submitted_at": "2025-10-26T10:00:00.000000Z",
    "reviewed_at": "2025-10-28T10:00:00.000000Z",
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "status": "active"
    },
    "admin_notes": "All documents verified. Registration approved."
  }
}
```

**Response if Rejected:**
```json
{
  "status": "success",
  "data": {
    "registration_id": 1,
    "status": "rejected",
    "submitted_at": "2025-10-26T10:00:00.000000Z",
    "reviewed_at": "2025-10-28T10:00:00.000000Z",
    "rejection_reason": "Business license is not valid",
    "admin_notes": "Please resubmit with valid business license",
    "can_resubmit": true
  }
}
```

---

## 🏨 Owner - Hotel Management

### 1. Get Hotel Detail

Mendapatkan detail hotel milik owner.

**Endpoint:** `GET /api/hotel`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta",
    "description": "Hotel mewah di pusat kota Jakarta dengan fasilitas lengkap",
    "address": "Jl. Sudirman No. 1",
    "country": {
      "id": 1,
      "name": "Indonesia",
      "code": "ID"
    },
    "phone": "021-1234567",
    "email": "info@grandhotel.com",
    "website": "https://grandhotel.com",
    "star_rating": 5,
    "status": "active",
    "check_in_time": "14:00:00",
    "check_out_time": "12:00:00",
    "images": [
      {
        "id": 1,
        "image_url": "storage/hotels/hotel1_1.jpg",
        "is_primary": true
      }
    ],
    "amenities": [
      {
        "id": 1,
        "name": "WiFi",
        "icon": "wifi"
      }
    ],
    "registration_status": "approved",
    "total_rooms": 50,
    "available_rooms": 30,
    "occupied_rooms": 15,
    "maintenance_rooms": 5,
    "created_at": "2025-10-26T10:00:00.000000Z",
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 2. Get Registration Status Only

Mendapatkan status registrasi hotel saja.

**Endpoint:** `GET /api/hotel/registration-status`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "registration_status": "approved",
    "hotel_status": "active",
    "can_manage_hotel": true,
    "reviewed_at": "2025-10-28T10:00:00.000000Z"
  }
}
```

---

### 3. Get Hotel Statistics

Mendapatkan statistik hotel.

**Endpoint:** `GET /api/hotel/statistics`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_rooms": 50,
    "available_rooms": 30,
    "occupied_rooms": 15,
    "maintenance_rooms": 5,
    "total_bookings": 500,
    "active_bookings": 20,
    "total_revenue": 250000000,
    "revenue_this_month": 25000000,
    "revenue_last_month": 20000000,
    "occupancy_rate": 70.5,
    "average_booking_value": 500000,
    "top_room_type": "Deluxe"
  }
}
```

---

### 4. Update Hotel

Mengupdate informasi hotel.

**Endpoint:** `PUT /api/hotel`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Grand Hotel Jakarta Updated",
  "description": "Updated description",
  "address": "Jl. Sudirman No. 1",
  "phone": "021-1234567",
  "email": "info@grandhotel.com",
  "website": "https://grandhotel.com",
  "check_in_time": "14:00",
  "check_out_time": "12:00"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Hotel information updated successfully",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta Updated",
    "description": "Updated description",
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

### 5. Toggle Hotel Status

Mengaktifkan/menonaktifkan hotel.

**Endpoint:** `POST /api/hotel/toggle-status`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "status": "inactive"
}
```

**Note:** Status yang tersedia: `active`, `inactive`

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Hotel status updated successfully",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta",
    "status": "inactive",
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

## 📊 Owner - Dashboard

### 1. Get Dashboard Overview

Mendapatkan overview dashboard owner.

**Endpoint:** `GET /api/dashboard`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta",
      "status": "active"
    },
    "summary": {
      "total_rooms": 50,
      "available_rooms": 30,
      "occupied_rooms": 15,
      "total_bookings_today": 5,
      "total_revenue_today": 2500000,
      "total_revenue_this_month": 25000000,
      "pending_check_ins": 8,
      "pending_check_outs": 5
    },
    "recent_bookings": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "guest_name": "John Doe",
        "room_number": "101",
        "check_in_date": "2025-11-01",
        "check_out_date": "2025-11-05",
        "total_price": 2000000,
        "status": "confirmed"
      }
    ],
    "revenue_trend": {
      "labels": ["Week 1", "Week 2", "Week 3", "Week 4"],
      "data": [5000000, 6000000, 7000000, 7000000]
    }
  }
}
```

---

### 2. Get Quick Stats

Mendapatkan statistik cepat untuk dashboard.

**Endpoint:** `GET /api/dashboard/quick-stats`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "today": {
      "bookings": 5,
      "check_ins": 3,
      "check_outs": 2,
      "revenue": 2500000
    },
    "this_week": {
      "bookings": 25,
      "revenue": 12500000,
      "occupancy_rate": 75
    },
    "this_month": {
      "bookings": 100,
      "revenue": 50000000,
      "occupancy_rate": 70
    }
  }
}
```

---

## 💰 Owner - Financial Reports

### 1. Get Financial Summary

Mendapatkan ringkasan keuangan.

**Endpoint:** `GET /api/reports/financial-summary`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `start_date` (optional): Tanggal mulai (YYYY-MM-DD)
- `end_date` (optional): Tanggal akhir (YYYY-MM-DD)

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "period": {
      "start_date": "2025-10-01",
      "end_date": "2025-10-26"
    },
    "income": {
      "total_bookings": 100,
      "total_revenue": 50000000,
      "average_booking_value": 500000
    },
    "expenses": {
      "total_expenses": 15000000,
      "by_category": {
        "operational": 8000000,
        "maintenance": 3000000,
        "utilities": 2000000,
        "staff": 2000000
      }
    },
    "profit": {
      "gross_profit": 50000000,
      "net_profit": 35000000,
      "profit_margin": 70
    }
  }
}
```

---

### 2. Get Monthly Trend

Mendapatkan tren bulanan.

**Endpoint:** `GET /api/reports/monthly-trend`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `months` (optional): Jumlah bulan (default: 6)

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "labels": ["May", "Jun", "Jul", "Aug", "Sep", "Oct"],
    "revenue": [40000000, 45000000, 48000000, 50000000, 52000000, 50000000],
    "expenses": [12000000, 13000000, 14000000, 15000000, 15500000, 15000000],
    "profit": [28000000, 32000000, 34000000, 35000000, 36500000, 35000000],
    "bookings": [80, 90, 95, 100, 105, 100]
  }
}
```

---

### 3. Get Expense Breakdown

Mendapatkan breakdown pengeluaran.

**Endpoint:** `GET /api/reports/expense-breakdown`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `start_date` (optional): Tanggal mulai
- `end_date` (optional): Tanggal akhir

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "period": {
      "start_date": "2025-10-01",
      "end_date": "2025-10-26"
    },
    "total_expenses": 15000000,
    "by_category": [
      {
        "category": "operational",
        "amount": 8000000,
        "percentage": 53.33,
        "count": 25
      },
      {
        "category": "maintenance",
        "amount": 3000000,
        "percentage": 20,
        "count": 8
      },
      {
        "category": "utilities",
        "amount": 2000000,
        "percentage": 13.33,
        "count": 12
      },
      {
        "category": "staff",
        "amount": 2000000,
        "percentage": 13.33,
        "count": 5
      }
    ]
  }
}
```

---

### 4. Get Recent Transactions

Mendapatkan transaksi terbaru.

**Endpoint:** `GET /api/reports/transactions`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `type` (optional): Tipe transaksi (income, expense, all) - default: all
- `limit` (optional): Jumlah data (default: 20)

**Response Success (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "type": "income",
      "description": "Booking Payment - BK-20251026-001",
      "amount": 2000000,
      "date": "2025-10-26",
      "booking_code": "BK-20251026-001",
      "guest_name": "John Doe"
    },
    {
      "id": 2,
      "type": "expense",
      "description": "Room maintenance",
      "amount": 500000,
      "category": "maintenance",
      "date": "2025-10-25"
    }
  ]
}
```

---

### 5. Get Income Performance

Mendapatkan performa pendapatan.

**Endpoint:** `GET /api/reports/income-performance`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` (optional): Period (week, month, year) - default: month

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_period": {
      "total_income": 50000000,
      "total_bookings": 100,
      "average_booking_value": 500000
    },
    "previous_period": {
      "total_income": 45000000,
      "total_bookings": 90,
      "average_booking_value": 500000
    },
    "growth": {
      "income_growth": 11.11,
      "bookings_growth": 11.11,
      "trend": "up"
    },
    "by_room_type": [
      {
        "room_type": "Deluxe",
        "bookings": 50,
        "revenue": 25000000,
        "percentage": 50
      },
      {
        "room_type": "Suite",
        "bookings": 30,
        "revenue": 18000000,
        "percentage": 36
      }
    ]
  }
}
```

---

### 6. Get Expense Performance

Mendapatkan performa pengeluaran.

**Endpoint:** `GET /api/reports/expense-performance`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` (optional): Period (week, month, year) - default: month

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_period": {
      "total_expenses": 15000000,
      "total_transactions": 50
    },
    "previous_period": {
      "total_expenses": 13000000,
      "total_transactions": 45
    },
    "growth": {
      "expense_growth": 15.38,
      "trend": "up"
    },
    "top_categories": [
      {
        "category": "operational",
        "amount": 8000000,
        "percentage": 53.33
      },
      {
        "category": "maintenance",
        "amount": 3000000,
        "percentage": 20
      }
    ]
  }
}
```

---

### 7. Get Report Summary (Legacy)

Mendapatkan ringkasan laporan keuangan.

**Endpoint:** `GET /api/reports/summary`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_revenue": 50000000,
    "total_expenses": 15000000,
    "net_profit": 35000000,
    "total_bookings": 100,
    "average_booking_value": 500000
  }
}
```

---

### 8. Get Report By Date (Legacy)

Mendapatkan laporan berdasarkan tanggal.

**Endpoint:** `GET /api/reports/by-date`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `start_date` (required): Tanggal mulai
- `end_date` (required): Tanggal akhir

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "period": {
      "start_date": "2025-10-01",
      "end_date": "2025-10-26"
    },
    "total_revenue": 50000000,
    "total_expenses": 15000000,
    "net_profit": 35000000,
    "total_bookings": 100
  }
}
```

---

### 9. Get Bookings Report (Legacy)

Mendapatkan laporan booking.

**Endpoint:** `GET /api/reports/bookings`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): Filter by status
- `date_from` (optional): Dari tanggal
- `date_to` (optional): Sampai tanggal

**Response Success (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "booking_code": "BK-20251026-001",
      "guest_name": "John Doe",
      "room_number": "101",
      "check_in_date": "2025-11-01",
      "check_out_date": "2025-11-05",
      "total_price": 2000000,
      "status": "confirmed",
      "payment_status": "paid",
      "created_at": "2025-10-26T09:00:00.000000Z"
    }
  ]
}
```

---

## 💸 Owner - Expense Management

### 1. Get All Expenses

Mendapatkan semua pengeluaran.

**Endpoint:** `GET /api/expenses`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `category` (optional): Filter by category (operational, maintenance, utilities, staff, other)
- `start_date` (optional): Filter dari tanggal
- `end_date` (optional): Filter sampai tanggal
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "hotel": {
          "id": 1,
          "name": "Grand Hotel Jakarta"
        },
        "category": "maintenance",
        "description": "Room 101 AC repair",
        "amount": 500000,
        "expense_date": "2025-10-25",
        "receipt_url": "storage/receipts/receipt1.pdf",
        "notes": "AC unit replaced",
        "created_by": {
          "id": 10,
          "name": "Owner Name"
        },
        "created_at": "2025-10-25T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 50,
    "last_page": 4
  }
}
```

---

### 2. Get Expense Detail

Mendapatkan detail pengeluaran.

**Endpoint:** `GET /api/expenses/{id}`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "hotel": {
      "id": 1,
      "name": "Grand Hotel Jakarta"
    },
    "category": "maintenance",
    "description": "Room 101 AC repair",
    "amount": 500000,
    "expense_date": "2025-10-25",
    "receipt_url": "storage/receipts/receipt1.pdf",
    "notes": "AC unit replaced",
    "created_by": {
      "id": 10,
      "name": "Owner Name",
      "email": "owner@example.com"
    },
    "created_at": "2025-10-25T10:00:00.000000Z",
    "updated_at": "2025-10-25T10:00:00.000000Z"
  }
}
```

---

### 3. Create Expense

Membuat pengeluaran baru.

**Endpoint:** `POST /api/expenses`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
category: maintenance
description: Room 101 AC repair
amount: 500000
expense_date: 2025-10-25
receipt: [file] (optional - PDF or Image)
notes: AC unit replaced (optional)
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Expense created successfully",
  "data": {
    "id": 1,
    "category": "maintenance",
    "description": "Room 101 AC repair",
    "amount": 500000,
    "expense_date": "2025-10-25",
    "receipt_url": "storage/receipts/receipt1.pdf",
    "created_at": "2025-10-25T10:00:00.000000Z"
  }
}
```

---

### 4. Update Expense

Mengupdate pengeluaran.

**Endpoint:** `PUT /api/expenses/{id}`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "category": "maintenance",
  "description": "Room 101 AC repair - Updated",
  "amount": 550000,
  "expense_date": "2025-10-25",
  "notes": "AC unit replaced with warranty"
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Expense updated successfully",
  "data": {
    "id": 1,
    "category": "maintenance",
    "description": "Room 101 AC repair - Updated",
    "amount": 550000,
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 5. Delete Expense

Menghapus pengeluaran.

**Endpoint:** `DELETE /api/expenses/{id}`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Expense deleted successfully"
}
```

---

### 6. Get Expense Statistics

Mendapatkan statistik pengeluaran.

**Endpoint:** `GET /api/expenses/statistics`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `start_date` (optional): Dari tanggal
- `end_date` (optional): Sampai tanggal

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_expenses": 15000000,
    "total_transactions": 50,
    "by_category": {
      "operational": {
        "amount": 8000000,
        "count": 25,
        "percentage": 53.33
      },
      "maintenance": {
        "amount": 3000000,
        "count": 8,
        "percentage": 20
      },
      "utilities": {
        "amount": 2000000,
        "count": 12,
        "percentage": 13.33
      },
      "staff": {
        "amount": 2000000,
        "count": 5,
        "percentage": 13.33
      }
    },
    "average_expense": 300000,
    "highest_expense": {
      "id": 1,
      "description": "Kitchen equipment",
      "amount": 2000000,
      "date": "2025-10-15"
    }
  }
}
```

---

## 🛏️ Owner - Room Management

### 1. Get All Rooms

Mendapatkan semua kamar milik hotel owner.

**Endpoint:** `GET /api/rooms`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `type` (optional): Filter by room type
- `status` (optional): Filter by status (available, occupied, maintenance)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "room_number": "101",
        "type": "Deluxe",
        "capacity": 2,
        "price_per_night": 500000,
        "status": "available",
        "description": "Kamar deluxe dengan pemandangan kota",
        "size": "32 sqm",
        "bed_type": "King Size",
        "main_image": "storage/rooms/room1.jpg",
        "amenities_count": 5,
        "total_bookings": 50,
        "total_revenue": 25000000,
        "created_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 50,
    "last_page": 4
  }
}
```

---

### 2. Get Room Detail

Mendapatkan detail kamar.

**Endpoint:** `GET /api/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "room_number": "101",
    "type": "Deluxe",
    "capacity": 2,
    "price_per_night": 500000,
    "status": "available",
    "description": "Kamar deluxe dengan pemandangan kota dan fasilitas lengkap",
    "size": "32 sqm",
    "bed_type": "King Size",
    "images": [
      {
        "id": 1,
        "image_url": "storage/rooms/room1_1.jpg",
        "is_primary": true
      },
      {
        "id": 2,
        "image_url": "storage/rooms/room1_2.jpg",
        "is_primary": false
      }
    ],
    "amenities": [
      {
        "id": 1,
        "name": "AC",
        "icon": "air_conditioner"
      },
      {
        "id": 2,
        "name": "TV",
        "icon": "tv"
      }
    ],
    "statistics": {
      "total_bookings": 50,
      "total_revenue": 25000000,
      "occupancy_rate": 75,
      "average_booking_value": 500000
    },
    "recent_bookings": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "guest_name": "John Doe",
        "check_in_date": "2025-11-01",
        "check_out_date": "2025-11-05",
        "status": "confirmed"
      }
    ],
    "created_at": "2025-10-26T10:00:00.000000Z",
    "updated_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 3. Create Room

Membuat kamar baru (termasuk gambar dan amenities).

**Endpoint:** `POST /api/rooms`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
room_number: 101
type: Deluxe
capacity: 2
price_per_night: 500000
status: available
description: Kamar deluxe dengan pemandangan kota
size: 32 sqm
bed_type: King Size
amenity_ids: [1,2,3,4] (JSON array)
images[]: [file] (multiple images, min: 1, max: 5)
primary_image_index: 0
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Room created successfully",
  "data": {
    "id": 1,
    "room_number": "101",
    "type": "Deluxe",
    "capacity": 2,
    "price_per_night": 500000,
    "status": "available",
    "images_count": 3,
    "amenities_count": 4,
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 4. Update Room

Mengupdate kamar (termasuk gambar dan amenities).

**Endpoint:** `PUT /api/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "room_number": "101A",
  "type": "Deluxe",
  "capacity": 2,
  "price_per_night": 550000,
  "status": "available",
  "description": "Updated description",
  "size": "35 sqm",
  "bed_type": "King Size",
  "amenity_ids": [1, 2, 3, 4, 5]
}
```

**Note:** Untuk update gambar, gunakan endpoint terpisah atau form-data

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Room updated successfully",
  "data": {
    "id": 1,
    "room_number": "101A",
    "type": "Deluxe",
    "price_per_night": 550000,
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

### 5. Delete Room

Menghapus kamar (soft delete).

**Endpoint:** `DELETE /api/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Room deleted successfully"
}
```

**Response Error (400):**
```json
{
  "status": "error",
  "message": "Cannot delete room with active bookings"
}
```

---

### 6. Update Room Status

Mengupdate status kamar.

**Endpoint:** `PATCH /api/rooms/{id}/status`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "status": "maintenance",
  "reason": "AC repair needed"
}
```

**Note:** Status yang tersedia: `available`, `occupied`, `maintenance`

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Room status updated successfully",
  "data": {
    "id": 1,
    "room_number": "101",
    "status": "maintenance",
    "updated_at": "2025-10-26T11:00:00.000000Z"
  }
}
```

---

### 7. Get Room Statistics

Mendapatkan statistik kamar keseluruhan.

**Endpoint:** `GET /api/rooms/statistics`

**Authentication:** Bearer Token (Required) - Role: Owner

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_rooms": 50,
    "available_rooms": 30,
    "occupied_rooms": 15,
    "maintenance_rooms": 5,
    "occupancy_rate": 70,
    "by_room_type": {
      "Deluxe": {
        "total": 20,
        "available": 12,
        "occupied": 6,
        "maintenance": 2,
        "revenue": 30000000
      },
      "Suite": {
        "total": 15,
        "available": 9,
        "occupied": 5,
        "maintenance": 1,
        "revenue": 25000000
      },
      "Standard": {
        "total": 15,
        "available": 9,
        "occupied": 4,
        "maintenance": 2,
        "revenue": 15000000
      }
    },
    "top_performing_rooms": [
      {
        "id": 1,
        "room_number": "101",
        "type": "Deluxe",
        "total_bookings": 50,
        "total_revenue": 25000000,
        "occupancy_rate": 85
      }
    ]
  }
}
```

---

# 👨‍💼 RECEPTIONIST ENDPOINTS

Semua endpoint receptionist memerlukan authentication dengan role **Receptionist** dan menggunakan prefix `/api/receptionist`.

---

## 🛏️ Receptionist - Room Management

### 1. Get All Rooms

Mendapatkan daftar semua kamar untuk receptionist.

**Endpoint:** `GET /api/receptionist/rooms`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `type` (optional): Filter by room type
- `status` (optional): Filter by status (available, occupied, maintenance)
- `search` (optional): Search by room number
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "room_number": "101",
        "type": "Deluxe",
        "capacity": 2,
        "price_per_night": 500000,
        "status": "available",
        "description": "Kamar deluxe dengan pemandangan kota",
        "size": "32 sqm",
        "bed_type": "King Size",
        "main_image": "storage/rooms/room1.jpg",
        "amenities": [
          {
            "id": 1,
            "name": "AC",
            "icon": "air_conditioner"
          },
          {
            "id": 2,
            "name": "TV",
            "icon": "tv"
          }
        ],
        "current_booking": null
      }
    ],
    "per_page": 15,
    "total": 50,
    "last_page": 4
  }
}
```

---

### 2. Get Room Detail

Mendapatkan detail kamar untuk receptionist.

**Endpoint:** `GET /api/receptionist/rooms/{id}`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "room_number": "101",
    "type": "Deluxe",
    "capacity": 2,
    "price_per_night": 500000,
    "status": "available",
    "description": "Kamar deluxe dengan pemandangan kota dan fasilitas lengkap",
    "size": "32 sqm",
    "bed_type": "King Size",
    "images": [
      {
        "id": 1,
        "image_url": "storage/rooms/room1_1.jpg",
        "is_primary": true
      }
    ],
    "amenities": [
      {
        "id": 1,
        "name": "AC",
        "icon": "air_conditioner",
        "description": "Air conditioning"
      }
    ],
    "current_booking": {
      "id": 1,
      "booking_code": "BK-20251026-001",
      "guest_name": "John Doe",
      "check_in_date": "2025-11-01",
      "check_out_date": "2025-11-05",
      "status": "confirmed"
    },
    "upcoming_bookings": [
      {
        "id": 2,
        "booking_code": "BK-20251026-002",
        "guest_name": "Jane Doe",
        "check_in_date": "2025-11-06",
        "check_out_date": "2025-11-10",
        "status": "confirmed"
      }
    ]
  }
}
```

---

## 📅 Receptionist - Booking Management

### 1. Get All Bookings

Mendapatkan semua booking untuk management oleh receptionist.

**Endpoint:** `GET /api/receptionist/bookings`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): Filter by status (pending, confirmed, checked_in, checked_out, cancelled)
- `room_id` (optional): Filter by room
- `date` (optional): Filter by date (check-in or check-out)
- `search` (optional): Search by booking code or guest name
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "guest": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com",
          "phone": "081234567890"
        },
        "room": {
          "id": 1,
          "room_number": "101",
          "type": "Deluxe"
        },
        "check_in_date": "2025-11-01",
        "check_out_date": "2025-11-05",
        "number_of_guests": 2,
        "total_nights": 4,
        "total_price": 2000000,
        "status": "confirmed",
        "payment_status": "paid",
        "special_requests": "Late check-in",
        "created_at": "2025-10-26T09:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 200,
    "last_page": 14
  }
}
```

---

### 2. Create Booking (Walk-in)

Membuat booking baru untuk walk-in guest (receptionist dapat membuat booking langsung).

**Endpoint:** `POST /api/receptionist/bookings`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "081234567890",
  "room_id": 1,
  "check_in_date": "2025-11-01",
  "check_out_date": "2025-11-05",
  "number_of_guests": 2,
  "special_requests": "Early check-in if possible",
  "payment_method": "cash",
  "paid_amount": 2000000
}
```

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Booking created successfully",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "guest": {
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890"
    },
    "room": {
      "id": 1,
      "room_number": "101",
      "type": "Deluxe"
    },
    "check_in_date": "2025-11-01",
    "check_out_date": "2025-11-05",
    "number_of_guests": 2,
    "total_nights": 4,
    "total_price": 2000000,
    "status": "confirmed",
    "payment": {
      "id": 1,
      "amount": 2000000,
      "payment_method": "cash",
      "status": "paid"
    },
    "created_at": "2025-10-26T10:00:00.000000Z"
  }
}
```

---

### 3. Check-In Guest

Melakukan check-in untuk booking yang sudah confirmed.

**Endpoint:** `POST /api/receptionist/bookings/{id}/check-in`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "actual_check_in_time": "14:30",
  "notes": "Guest arrived on time. Room prepared."
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Guest checked in successfully",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "guest": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890"
    },
    "room": {
      "id": 1,
      "room_number": "101",
      "type": "Deluxe",
      "status": "occupied"
    },
    "check_in_date": "2025-11-01",
    "actual_check_in_time": "2025-11-01 14:30:00",
    "check_out_date": "2025-11-05",
    "status": "checked_in",
    "checked_in_by": {
      "id": 5,
      "name": "Receptionist Name"
    },
    "notes": "Guest arrived on time. Room prepared."
  }
}
```

**Response Error (404):**
```json
{
  "message": "Booking tidak ditemukan"
}
```

**Response Error (400):**
```json
{
  "status": "error",
  "message": "Booking must be confirmed before check-in"
}
```

---

### 4. Check-Out Guest

Melakukan check-out untuk guest yang sudah checked-in.

**Endpoint:** `POST /api/receptionist/bookings/{booking}/check-out`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "actual_check_out_time": "11:45",
  "additional_charges": 100000,
  "additional_charges_description": "Minibar and room service",
  "room_condition": "good",
  "notes": "No damages. Room in good condition."
}
```

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Guest checked out successfully",
  "data": {
    "id": 1,
    "booking_code": "BK-20251026-001",
    "guest": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "room": {
      "id": 1,
      "room_number": "101",
      "type": "Deluxe",
      "status": "available"
    },
    "check_in_date": "2025-11-01",
    "check_out_date": "2025-11-05",
    "actual_check_out_time": "2025-11-05 11:45:00",
    "status": "checked_out",
    "original_total": 2000000,
    "additional_charges": 100000,
    "final_total": 2100000,
    "checked_out_by": {
      "id": 5,
      "name": "Receptionist Name"
    },
    "room_condition": "good",
    "notes": "No damages. Room in good condition."
  }
}
```

**Response Error (404):**
```json
{
  "message": "Booking tidak ditemukan"
}
```

**Response Error (400):**
```json
{
  "status": "error",
  "message": "Booking must be checked-in before check-out"
}
```

---

### 5. Get Checked-In Bookings

Mendapatkan daftar booking yang sudah checked-in (guest sedang menginap).

**Endpoint:** `GET /api/receptionist/bookings/checked-in`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "guest": {
          "id": 1,
          "name": "John Doe",
          "phone": "081234567890"
        },
        "room": {
          "id": 1,
          "room_number": "101",
          "type": "Deluxe"
        },
        "check_in_date": "2025-11-01",
        "actual_check_in_time": "2025-11-01 14:30:00",
        "check_out_date": "2025-11-05",
        "nights_stayed": 2,
        "remaining_nights": 2,
        "status": "checked_in"
      }
    ],
    "per_page": 15,
    "total": 20,
    "last_page": 2
  }
}
```

---

### 6. Get Checked-Out Bookings

Mendapatkan daftar booking yang sudah checked-out.

**Endpoint:** `GET /api/receptionist/bookings/checked-out`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `date` (optional): Filter by check-out date (YYYY-MM-DD)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "guest": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "room": {
          "id": 1,
          "room_number": "101",
          "type": "Deluxe"
        },
        "check_in_date": "2025-11-01",
        "check_out_date": "2025-11-05",
        "actual_check_out_time": "2025-11-05 11:45:00",
        "total_nights": 4,
        "final_total": 2100000,
        "status": "checked_out",
        "checked_out_by": {
          "id": 5,
          "name": "Receptionist Name"
        }
      }
    ],
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

---

## 💳 Receptionist - Payment Management

### 1. Process Payment

Memproses pembayaran untuk booking (walk-in atau additional charges).

**Endpoint:** `POST /api/receptionist/payments`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "booking_id": 1,
  "amount": 2000000,
  "payment_method": "cash",
  "notes": "Full payment received"
}
```

**Available Payment Methods:**
- `cash`: Tunai
- `debit_card`: Kartu Debit
- `credit_card`: Kartu Kredit
- `bank_transfer`: Transfer Bank

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Payment processed successfully",
  "data": {
    "payment_id": 1,
    "booking": {
      "id": 1,
      "booking_code": "BK-20251026-001"
    },
    "amount": 2000000,
    "payment_method": "cash",
    "status": "paid",
    "processed_by": {
      "id": 5,
      "name": "Receptionist Name"
    },
    "transaction_date": "2025-10-26T10:00:00.000000Z",
    "receipt_number": "RCP-20251026-001"
  }
}
```

---

## 📊 Receptionist - Dashboard

### 1. Get Dashboard Stats

Mendapatkan statistik dashboard receptionist.

**Endpoint:** `GET /api/receptionist/dashboard/stats`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "today": {
      "check_ins": 8,
      "check_outs": 5,
      "new_bookings": 3,
      "total_revenue": 5000000
    },
    "current": {
      "checked_in_guests": 20,
      "available_rooms": 30,
      "occupied_rooms": 15,
      "maintenance_rooms": 5,
      "occupancy_rate": 70
    },
    "upcoming": {
      "check_ins_today": 8,
      "check_outs_today": 5,
      "check_ins_tomorrow": 10
    }
  }
}
```

---

### 2. Get Room Status Data

Mendapatkan data status kamar untuk dashboard.

**Endpoint:** `GET /api/receptionist/dashboard/room-status`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "total_rooms": 50,
    "available": 30,
    "occupied": 15,
    "maintenance": 5,
    "by_type": {
      "Deluxe": {
        "total": 20,
        "available": 12,
        "occupied": 6,
        "maintenance": 2
      },
      "Suite": {
        "total": 15,
        "available": 9,
        "occupied": 5,
        "maintenance": 1
      },
      "Standard": {
        "total": 15,
        "available": 9,
        "occupied": 4,
        "maintenance": 2
      }
    }
  }
}
```

---

### 3. Get Reservation Trends

Mendapatkan tren reservasi untuk dashboard.

**Endpoint:** `GET /api/receptionist/dashboard/reservation-trends`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` (optional): Period (week, month) - default: week

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "period": "week",
    "labels": ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    "check_ins": [5, 8, 6, 10, 12, 15, 10],
    "check_outs": [3, 5, 8, 7, 9, 10, 8],
    "bookings": [4, 6, 5, 8, 10, 12, 7]
  }
}
```

---

### 4. Get Payment Methods Data

Mendapatkan data metode pembayaran untuk dashboard.

**Endpoint:** `GET /api/receptionist/dashboard/payment-methods`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` (optional): Period (today, week, month) - default: today

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "period": "today",
    "total_transactions": 20,
    "total_amount": 10000000,
    "by_method": {
      "cash": {
        "count": 10,
        "amount": 5000000,
        "percentage": 50
      },
      "credit_card": {
        "count": 6,
        "amount": 3000000,
        "percentage": 30
      },
      "debit_card": {
        "count": 3,
        "amount": 1500000,
        "percentage": 15
      },
      "bank_transfer": {
        "count": 1,
        "amount": 500000,
        "percentage": 5
      }
    }
  }
}
```

---

### 5. Get Today's Activities

Mendapatkan aktivitas hari ini untuk dashboard.

**Endpoint:** `GET /api/receptionist/dashboard/todays-activities`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "upcoming_check_ins": [
      {
        "id": 1,
        "booking_code": "BK-20251026-001",
        "guest_name": "John Doe",
        "room_number": "101",
        "check_in_time": "14:00",
        "special_requests": "Late check-in"
      }
    ],
    "upcoming_check_outs": [
      {
        "id": 2,
        "booking_code": "BK-20251025-005",
        "guest_name": "Jane Smith",
        "room_number": "205",
        "check_out_time": "12:00"
      }
    ],
    "recent_bookings": [
      {
        "id": 3,
        "booking_code": "BK-20251026-003",
        "guest_name": "Bob Wilson",
        "room_number": "303",
        "created_at": "2025-10-26T09:30:00.000000Z",
        "status": "confirmed"
      }
    ]
  }
}
```

---

### 6. Get Monthly Revenue

Mendapatkan data revenue bulanan untuk dashboard.

**Endpoint:** `GET /api/receptionist/dashboard/monthly-revenue`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `months` (optional): Jumlah bulan (default: 6)

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "labels": ["May", "Jun", "Jul", "Aug", "Sep", "Oct"],
    "revenue": [40000000, 45000000, 48000000, 50000000, 52000000, 50000000],
    "bookings": [80, 90, 95, 100, 105, 100],
    "current_month": {
      "revenue": 50000000,
      "bookings": 100,
      "average_per_booking": 500000
    },
    "previous_month": {
      "revenue": 52000000,
      "bookings": 105
    },
    "growth": {
      "revenue_growth": -3.85,
      "bookings_growth": -4.76
    }
  }
}
```

---

### 7. Get Occupancy Rate

Mendapatkan tingkat occupancy untuk dashboard.

**Endpoint:** `GET /api/receptionist/dashboard/occupancy-rate`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` (optional): Period (week, month) - default: week

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "period": "week",
    "current_occupancy": 70,
    "labels": ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    "occupancy_rates": [65, 68, 72, 75, 78, 80, 70],
    "average_occupancy": 72.57,
    "peak_day": {
      "day": "Saturday",
      "rate": 80
    },
    "lowest_day": {
      "day": "Monday",
      "rate": 65
    }
  }
}
```

---

### 8. Get All Dashboard Data

Mendapatkan semua data dashboard sekaligus (kombinasi semua endpoint dashboard).

**Endpoint:** `GET /api/receptionist/dashboard/all`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "stats": { "..." },
    "room_status": { "..." },
    "reservation_trends": { "..." },
    "payment_methods": { "..." },
    "todays_activities": { "..." },
    "monthly_revenue": { "..." },
    "occupancy_rate": { "..." }
  }
}
```

---

## 👥 Receptionist - Guest Management

### 1. Get All Guests

Mendapatkan informasi semua tamu/guest.

**Endpoint:** `GET /api/receptionist/guests`

**Authentication:** Bearer Token (Required) - Role: Receptionist

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `search` (optional): Search by name, email, or phone
- `status` (optional): Filter by status (checked_in, checked_out, upcoming)
- `per_page` (optional): Jumlah data per halaman

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "081234567890",
        "current_booking": {
          "id": 1,
          "booking_code": "BK-20251026-001",
          "room_number": "101",
          "status": "checked_in",
          "check_in_date": "2025-11-01",
          "check_out_date": "2025-11-05"
        },
        "total_bookings": 5,
        "total_spent": 10000000,
        "last_stay": "2025-09-15",
        "vip_status": false
      },
      {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "phone": "081234567891",
        "current_booking": null,
        "total_bookings": 12,
        "total_spent": 30000000,
        "last_stay": "2025-10-20",
        "vip_status": true
      }
    ],
    "per_page": 15,
    "total": 500,
    "last_page": 34
  }
}
```

---

## 📋 Status Codes (Receptionist)

| Status Code | Deskripsi |
|------------|-----------|
| 200 | OK - Request berhasil |
| 201 | Created - Resource berhasil dibuat |
| 400 | Bad Request - Request tidak valid |
| 401 | Unauthorized - Authentication gagal |
| 403 | Forbidden - Tidak memiliki akses (bukan receptionist) |
| 404 | Not Found - Resource tidak ditemukan |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Error di server |

---

## 🔄 Booking Status Flow (Receptionist View)

```
Walk-in Booking → confirmed (if paid) → checked_in → checked_out
       ↓
    pending (if not paid immediately) → confirmed → checked_in → checked_out
```

**Status Explanation:**
- `pending`: Booking dibuat, menunggu pembayaran
- `confirmed`: Pembayaran berhasil, booking dikonfirmasi
- `checked_in`: Tamu sudah check-in (receptionist processed)
- `checked_out`: Tamu sudah check-out (receptionist processed)
- `cancelled`: Booking dibatalkan

---

## 🏨 Room Status (Receptionist Management)

**Status yang dikelola receptionist:**
- `available`: Kamar tersedia untuk booking
- `occupied`: Kamar sedang ditempati (ada guest checked-in)
- `maintenance`: Kamar dalam perbaikan (tidak bisa di-booking)

**Status transitions:**
- `available` → `occupied` (saat check-in)
- `occupied` → `available` (saat check-out)
- Any status → `maintenance` (jika ada kerusakan/perbaikan)
- `maintenance` → `available` (setelah perbaikan selesai)

---

## 💡 Notes untuk Receptionist

1. **Walk-in Bookings**: Receptionist dapat membuat booking langsung untuk tamu walk-in
2. **Check-in Process**: Hanya booking dengan status `confirmed` yang bisa di-check-in
3. **Check-out Process**: Hanya booking dengan status `checked_in` yang bisa di-check-out
4. **Payment Processing**: Receptionist dapat memproses pembayaran tunai atau kartu
5. **Additional Charges**: Bisa ditambahkan saat check-out (minibar, room service, dll)
6. **Room Status**: Otomatis berubah saat check-in (occupied) dan check-out (available)
7. **Guest Information**: Akses ke informasi guest untuk memberikan layanan yang lebih baik

---

**Dokumentasi ini mencakup semua endpoint dari file `api.php`, `api_admin.php`, `api_owner.php`, dan `api_receptionist.php`**

**Created:** October 26, 2025
**Version:** 1.0.0
**Last Updated:** October 26, 2025
