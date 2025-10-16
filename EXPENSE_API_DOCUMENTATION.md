# Owner Expense Management API Documentation

Base URL: `http://127.0.0.1:8000/api/owner`

All endpoints require authentication with Bearer token in Authorization header.

## Endpoints

### 1. Get All Expenses
**GET** `/expenses`

Returns all expenses for the owner's hotel.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "category": "Operasional",
      "amount": 5000000,
      "description": "Pembelian perlengkapan hotel (handuk, sprei, amenities)",
      "date": "2025-10-10",
      "payment_method": "Transfer Bank",
      "receipt_number": "INV-2025-001",
      "created_at": "2025-10-10T10:30:00.000000Z"
    }
  ]
}
```

### 2. Create New Expense
**POST** `/expenses`

Creates a new expense record.

**Request Body:**
```json
{
  "category": "Operasional",
  "amount": 5000000,
  "description": "Pembelian perlengkapan hotel",
  "date": "2025-10-10",
  "payment_method": "Transfer Bank",
  "receipt_number": "INV-2025-001"
}
```

**Validation Rules:**
- `category`: required, must be one of: Gaji, Operasional, Maintenance, Utilitas, Marketing, Supplies, Lain-lain
- `amount`: required, numeric, minimum 0
- `description`: required, string, max 1000 characters
- `date`: required, valid date format
- `payment_method`: optional, must be one of: Cash, Transfer Bank, Credit Card, Debit Card
- `receipt_number`: optional, string, max 255 characters

**Response (201):**
```json
{
  "message": "Pengeluaran berhasil ditambahkan",
  "data": {
    "id": 1,
    "category": "Operasional",
    "amount": 5000000,
    "description": "Pembelian perlengkapan hotel",
    "date": "2025-10-10",
    "payment_method": "Transfer Bank",
    "receipt_number": "INV-2025-001",
    "created_at": "2025-10-10T10:30:00.000000Z"
  }
}
```

### 3. Get Single Expense
**GET** `/expenses/{id}`

Returns details of a specific expense.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "category": "Operasional",
    "amount": 5000000,
    "description": "Pembelian perlengkapan hotel",
    "date": "2025-10-10",
    "payment_method": "Transfer Bank",
    "receipt_number": "INV-2025-001",
    "created_at": "2025-10-10T10:30:00.000000Z"
  }
}
```

### 4. Update Expense
**PUT** `/expenses/{id}`

Updates an existing expense.

**Request Body:** (same as Create)
```json
{
  "category": "Operasional",
  "amount": 5500000,
  "description": "Updated description",
  "date": "2025-10-10",
  "payment_method": "Transfer Bank",
  "receipt_number": "INV-2025-001"
}
```

**Response (200):**
```json
{
  "message": "Pengeluaran berhasil diperbarui",
  "data": { ... }
}
```

### 5. Delete Expense
**DELETE** `/expenses/{id}`

Deletes an expense record.

**Response (200):**
```json
{
  "message": "Pengeluaran berhasil dihapus"
}
```

### 6. Get Expense Statistics
**GET** `/expenses/statistics`

Returns statistical data about expenses.

**Query Parameters:**
- `start_date`: optional, filter by start date (YYYY-MM-DD)
- `end_date`: optional, filter by end date (YYYY-MM-DD)
- `category`: optional, filter by category

**Response (200):**
```json
{
  "data": {
    "total_expenses": 27500000,
    "total_transactions": 5,
    "average_per_transaction": 5500000,
    "by_category": [
      {
        "category": "Gaji",
        "total": 15000000,
        "count": 1
      },
      {
        "category": "Operasional",
        "total": 5000000,
        "count": 1
      }
    ],
    "by_month": [
      {
        "month": "2025-10",
        "total": 27500000,
        "count": 5
      }
    ]
  }
}
```

## Error Responses

### 403 Forbidden
```json
{
  "message": "Anda bukan owner"
}
```

### 404 Not Found
```json
{
  "message": "Pengeluaran tidak ditemukan"
}
```

### 422 Validation Error
```json
{
  "message": "Validation failed",
  "errors": {
    "amount": ["The amount field is required."],
    "category": ["The selected category is invalid."]
  }
}
```

## Frontend Integration Example

```typescript
// Fetch all expenses
const fetchExpenses = async () => {
  const response = await axios.get(
    'http://127.0.0.1:8000/api/owner/expenses',
    {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    }
  );
  return response.data.data;
};

// Create expense
const createExpense = async (formData) => {
  const response = await axios.post(
    'http://127.0.0.1:8000/api/owner/expenses',
    formData,
    {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    }
  );
  return response.data;
};

// Update expense
const updateExpense = async (id, formData) => {
  const response = await axios.put(
    `http://127.0.0.1:8000/api/owner/expenses/${id}`,
    formData,
    {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    }
  );
  return response.data;
};

// Delete expense
const deleteExpense = async (id) => {
  const response = await axios.delete(
    `http://127.0.0.1:8000/api/owner/expenses/${id}`,
    {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    }
  );
  return response.data;
};

// Get statistics
const getStatistics = async () => {
  const response = await axios.get(
    'http://127.0.0.1:8000/api/owner/expenses/statistics',
    {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    }
  );
  return response.data.data;
};
```
