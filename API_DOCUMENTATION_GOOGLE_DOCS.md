# DOKUMENTASI API - HOTEL MANAGEMENT SYSTEM

Base URL: http://your-domain.com/api
Format: JSON
Authentication: Bearer Token (Laravel Sanctum)

═══════════════════════════════════════════════════════════════════════════════

## AUTHENTICATION ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 1. REGISTER (Sign Up)
POST /api/auth/signup

Request:
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890"
}

Response (201):
{
  "status": "success",
  "message": "User registered successfully",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
    "token": "1|laravel_sanctum_token"
  }
}

───────────────────────────────────────────────────────────────────────────────

### 2. LOGIN (Sign In)
POST /api/auth/signin

Request:
{
  "email": "john@example.com",
  "password": "password123"
}

Response (200):
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
    "token": "1|laravel_sanctum_token"
  }
}

───────────────────────────────────────────────────────────────────────────────

### 3. LOGOUT
POST /api/auth/logout
Headers: Authorization: Bearer {token}

Response (200):
{
  "status": "success",
  "message": "Logged out successfully"
}

═══════════════════════════════════════════════════════════════════════════════

## USER PROFILE ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 4. GET PROFILE
GET /api/auth/profile
Headers: Authorization: Bearer {token}

Response (200):
{
  "status": "success",
  "message": "Get profile success",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "role": "customer"
  }
}

───────────────────────────────────────────────────────────────────────────────

### 5. UPDATE PROFILE
PUT /api/auth/profile
Headers: Authorization: Bearer {token}

Request:
{
  "name": "John Updated",
  "phone": "081234567890",
  "address": "Jl. Example No. 123"
}

Response (200):
{
  "status": "success",
  "message": "Profile updated successfully",
  "data": { ... }
}

───────────────────────────────────────────────────────────────────────────────

### 6. CHANGE PASSWORD
POST /api/auth/change-password
Headers: Authorization: Bearer {token}

Request:
{
  "current_password": "password123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}

Response (200):
{
  "status": "success",
  "message": "Password changed successfully"
}

───────────────────────────────────────────────────────────────────────────────

### 7. DELETE ACCOUNT
DELETE /api/auth/profile
Headers: Authorization: Bearer {token}

Response (200):
{
  "status": "success",
  "message": "Account deleted successfully"
}

═══════════════════════════════════════════════════════════════════════════════

## COUNTRY ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 8. GET COUNTRIES
GET /api/countries
Query Parameters:
  - page: (optional) Page number, default 1
  - show_inactive: (optional) Include inactive countries

Response (200):
{
  "status": "success",
  "message": "Get countries",
  "data": [
    {
      "id": 1,
      "name": "Indonesia",
      "code": "ID",
      "description": "...",
      "image": "https://images.unsplash.com/...",
      "is_active": true,
      "hotelCount": 5
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 12,
    "total": 18,
    "last_page": 2
  }
}

═══════════════════════════════════════════════════════════════════════════════

## HOTEL ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 9. GET HOTELS (List & Search)
GET /api/hotels

Query Parameters:
  - search: Search by name, city, address
  - check_in: Check-in date (YYYY-MM-DD)
  - check_out: Check-out date (YYYY-MM-DD)
  - guests: Number of guests
  - room_type: single/double/deluxe/suite
  - rating: Minimum star rating (1-5)
  - star_rating: Exact star rating
  - min_price: Minimum price
  - max_price: Maximum price
  - country: Filter by country
  - city: Filter by city
  - amenities: Comma-separated amenity IDs
  - sort_by: name/price/rating
  - sort_order: asc/desc
  - per_page: Items per page (default 10)

Response (200):
{
  "status": "success",
  "message": "Get hotels success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Grand Hotel Jakarta",
        "description": "...",
        "address": "...",
        "city": "Jakarta",
        "country": "Indonesia",
        "star_rating": 5,
        "amenities": [...],
        "images": [...],
        "rooms": [...]
      }
    ],
    "total": 33,
    "per_page": 10
  }
}

NOTE: Hanya menampilkan hotel dengan owner status "approved"

───────────────────────────────────────────────────────────────────────────────

### 10. GET HOTEL DETAIL
GET /api/hotels/{id}

Response (200):
{
  "status": "success",
  "message": "Get detail hotel success",
  "data": {
    "id": 1,
    "name": "Grand Hotel Jakarta",
    "description": "...",
    "amenities": [...],
    "images": [...],
    "rooms": [
      {
        "id": 1,
        "room_number": "101",
        "room_type": "deluxe",
        "capacity": 2,
        "price_per_night": 500000,
        "status": "available",
        "amenities": [...],
        "images": [...]
      }
    ]
  }
}

Response (404) - if owner not approved:
{
  "status": "error",
  "message": "Hotel not available or owner not approved"
}

═══════════════════════════════════════════════════════════════════════════════

## ROOM ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 11. GET AVAILABLE ROOMS
GET /api/rooms/available

Query Parameters:
  - hotel_id: (required) Hotel ID
  - check_in: (required) YYYY-MM-DD
  - check_out: (required) YYYY-MM-DD
  - guests: Number of guests

Response (200):
{
  "status": "success",
  "message": "Get available rooms",
  "data": [
    {
      "id": 1,
      "room_number": "101",
      "room_type": "deluxe",
      "capacity": 2,
      "price_per_night": 500000,
      "total_price": 1500000,
      "status": "available"
    }
  ]
}

───────────────────────────────────────────────────────────────────────────────

### 12. GET ROOM DETAIL
GET /api/rooms/{id}

Response (200):
{
  "status": "success",
  "message": "Get detail room success",
  "data": {
    "id": 1,
    "hotel_id": 1,
    "room_number": "101",
    "room_type": "deluxe",
    "capacity": 2,
    "price_per_night": 500000,
    "status": "available",
    "hotel": {...},
    "amenities": [...],
    "images": [...]
  }
}

═══════════════════════════════════════════════════════════════════════════════

## BOOKING ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 13. CREATE BOOKING
POST /api/bookings
Headers: Authorization: Bearer {token}

Request:
{
  "room_id": 1,
  "check_in_date": "2025-11-01",
  "check_out_date": "2025-11-05",
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "081234567890"
}

Response (201):
{
  "status": "success",
  "message": "Booking created successfully",
  "data": {
    "id": 1,
    "room_id": 1,
    "user_id": 1,
    "check_in_date": "2025-11-01",
    "check_out_date": "2025-11-05",
    "total_price": 2000000,
    "status": "pending"
  }
}

───────────────────────────────────────────────────────────────────────────────

### 14. GET MY BOOKINGS
GET /api/bookings
Headers: Authorization: Bearer {token}

Query Parameters:
  - status: pending/confirmed/checked_in/checked_out/cancelled

Response (200):
{
  "status": "success",
  "message": "Get bookings success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "check_in_date": "2025-11-01",
        "check_out_date": "2025-11-05",
        "total_price": 2000000,
        "status": "pending",
        "room": {...},
        "hotel": {...},
        "payment": {...}
      }
    ]
  }
}

───────────────────────────────────────────────────────────────────────────────

### 15. GET BOOKING DETAIL
GET /api/bookings/{id}
Headers: Authorization: Bearer {token}

Response (200):
{
  "status": "success",
  "message": "Get booking detail success",
  "data": {
    "id": 1,
    "room_id": 1,
    "check_in_date": "2025-11-01",
    "check_out_date": "2025-11-05",
    "total_price": 2000000,
    "status": "pending",
    "room": {...},
    "hotel": {...},
    "payment": {...},
    "invoice": {...}
  }
}

───────────────────────────────────────────────────────────────────────────────

### 16. UPDATE BOOKING
PUT /api/bookings/{id}
Headers: Authorization: Bearer {token}

Request:
{
  "check_in_date": "2025-11-02",
  "check_out_date": "2025-11-06"
}

Response (200):
{
  "status": "success",
  "message": "Booking updated successfully",
  "data": {...}
}

───────────────────────────────────────────────────────────────────────────────

### 17. CANCEL BOOKING
DELETE /api/bookings/{id}
Headers: Authorization: Bearer {token}

Response (200):
{
  "status": "success",
  "message": "Booking cancelled successfully"
}

═══════════════════════════════════════════════════════════════════════════════

## PAYMENT ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 18. CREATE PAYMENT (Midtrans)
POST /api/payments
Headers: Authorization: Bearer {token}

Request:
{
  "booking_id": 1,
  "payment_method": "credit_card"
}

Response (201):
{
  "status": "success",
  "message": "Payment initiated",
  "data": {
    "payment_id": 1,
    "midtrans_order_id": "ORDER-20251026-00001",
    "snap_token": "midtrans_snap_token_here",
    "redirect_url": "https://app.midtrans.com/snap/v2/..."
  }
}

───────────────────────────────────────────────────────────────────────────────

### 19. GET PAYMENT STATUS
GET /api/payments/{id}
Headers: Authorization: Bearer {token}

Response (200):
{
  "status": "success",
  "message": "Get payment status",
  "data": {
    "id": 1,
    "booking_id": 1,
    "amount": 2000000,
    "status": "paid",
    "midtrans_order_id": "ORDER-20251026-00001",
    "transaction_date": "2025-10-26T10:00:00Z"
  }
}

───────────────────────────────────────────────────────────────────────────────

### 20. MIDTRANS CALLBACK (Webhook)
POST /api/payments/midtrans/callback

Request (from Midtrans):
{
  "order_id": "ORDER-20251026-00001",
  "status_code": "200",
  "transaction_status": "settlement",
  "gross_amount": "2000000.00"
}

Response (200):
{
  "status": "success",
  "message": "Payment notification processed"
}

═══════════════════════════════════════════════════════════════════════════════

## INVOICE ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 21. GET MY INVOICES
GET /api/invoices
Headers: Authorization: Bearer {token}

Response (200):
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "invoice_number": "INV-20251026-001",
      "amount": 2000000,
      "invoice_date": "2025-10-26",
      "booking": {...},
      "payment": {...}
    }
  ],
  "total": 5,
  "per_page": 10
}

NOTE: Hanya menampilkan invoice milik user yang login

───────────────────────────────────────────────────────────────────────────────

### 22. GET INVOICE DETAIL
GET /api/invoices/{id}
Headers: Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "data": {
    "id": 1,
    "invoice_number": "INV-20251026-001",
    "amount": 2000000,
    "invoice_date": "2025-10-26",
    "booking": {
      "hotel": {...},
      "room": {...}
    },
    "payment": {...}
  }
}

Response (404) - if not owned by user:
{
  "success": false,
  "message": "Invoice not found"
}

═══════════════════════════════════════════════════════════════════════════════

## AMENITY ENDPOINTS

═══════════════════════════════════════════════════════════════════════════════

### 23. GET AMENITIES
GET /api/amenities

Query Parameters:
  - type: hotel/room

Response (200):
{
  "status": "success",
  "message": "Get amenities success",
  "data": [
    {
      "id": 1,
      "name": "WiFi",
      "type": "hotel"
    },
    {
      "id": 2,
      "name": "Air Conditioning",
      "type": "room"
    }
  ]
}

═══════════════════════════════════════════════════════════════════════════════

## ERROR RESPONSES

═══════════════════════════════════════════════════════════════════════════════

### 400 - Bad Request
{
  "status": "error",
  "message": "Invalid request",
  "errors": { ... }
}

### 401 - Unauthorized
{
  "status": "error",
  "message": "Unauthenticated"
}

### 403 - Forbidden
{
  "status": "error",
  "message": "Access denied"
}

### 404 - Not Found
{
  "status": "error",
  "message": "Resource not found"
}

### 422 - Validation Error
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."]
  }
}

### 500 - Server Error
{
  "status": "error",
  "message": "Internal server error"
}

═══════════════════════════════════════════════════════════════════════════════

## AUTHENTICATION NOTES

═══════════════════════════════════════════════════════════════════════════════

1. Untuk endpoint yang memerlukan authentication, sertakan header:
   Authorization: Bearer {your_token}

2. Token didapat dari response login/register

3. Token bersifat personal, jangan dibagikan

4. Logout akan menghapus token dari server

═══════════════════════════════════════════════════════════════════════════════

## SEEDED TEST ACCOUNTS

═══════════════════════════════════════════════════════════════════════════════

ADMIN:
  Email: admin1@hotelmanagement.com
  Password: password123

OWNER (example):
  Email: owner+grandhoteljakarta@gmail.com
  Password: password123

RECEPTIONIST (example):
  Email: receptionist+grandhoteljakarta@gmail.com
  Password: password123

CUSTOMER (example):
  Email: customer1@example.com
  Password: password123

═══════════════════════════════════════════════════════════════════════════════

## DATABASE SEEDED DATA

═══════════════════════════════════════════════════════════════════════════════

- Countries: 18 (with images from Unsplash)
- Hotels: 33 (across Asia, Europe, North America)
- Rooms: 300+ (various types)
- Owners: 33 (all with status "approved")
- Receptionists: 45+
- Customers: 30
- Admins: 5
- Bookings: 50 (sample bookings with payments & invoices)
- Amenities: 20 (10 hotel, 10 room)

═══════════════════════════════════════════════════════════════════════════════

## IMPORTANT BUSINESS RULES

═══════════════════════════════════════════════════════════════════════════════

1. HOTEL VISIBILITY:
   - Hanya hotel dengan owner status "approved" yang muncul di API
   - Owner registration status: pending → step_1 → step_2 → step_3 → 
     step_4 → completed → approved/rejected

2. INVOICE ACCESS:
   - User hanya bisa melihat invoice miliknya sendiri
   - Invoice dibuat otomatis setelah payment berhasil

3. BOOKING RULES:
   - Check-out date harus setelah check-in date
   - Room harus available pada tanggal yang dipilih
   - Total price dihitung: (check_out - check_in) × price_per_night

4. PAYMENT INTEGRATION:
   - Menggunakan Midtrans Payment Gateway
   - Support: Credit Card, Bank Transfer, E-Wallet
   - Callback/webhook untuk update payment status

═══════════════════════════════════════════════════════════════════════════════

END OF DOCUMENTATION
Version: 1.0
Last Updated: October 26, 2025

═══════════════════════════════════════════════════════════════════════════════
