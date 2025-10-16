# Owner Financial Reports API Documentation

Base URL: `http://127.0.0.1:8000/api/owner`

All endpoints require authentication with Bearer token in Authorization header.

## Endpoints

### 1. Get Financial Summary
**GET** `/reports/financial-summary`

Returns comprehensive financial summary with income, expenses, and profit metrics.

**Query Parameters:**
- `year`: optional, filter by year (default: current year)
- `month`: optional, filter by specific month (1-12)
- `start_date`: optional, custom start date (YYYY-MM-DD)
- `end_date`: optional, custom end date (YYYY-MM-DD)

**Response (200):**
```json
{
  "data": {
    "total_income": 125000000,
    "total_expenses": 45000000,
    "net_profit": 80000000,
    "profit_margin": 64.00,
    "total_bookings": 156,
    "income_growth": 12.50,
    "expense_growth": 8.20,
    "period": {
      "from": "2025-01-01",
      "to": "2025-12-31"
    }
  }
}
```

### 2. Get Monthly Trend
**GET** `/reports/monthly-trend`

Returns monthly financial trends for the entire year.

**Query Parameters:**
- `year`: optional, filter by year (default: current year)

**Response (200):**
```json
{
  "data": [
    {
      "month": "Jan",
      "month_number": 1,
      "month_name": "Januari",
      "income": 95000000,
      "expenses": 35000000,
      "profit": 60000000
    },
    {
      "month": "Feb",
      "month_number": 2,
      "month_name": "Februari",
      "income": 88000000,
      "expenses": 32000000,
      "profit": 56000000
    }
    // ... more months
  ]
}
```

### 3. Get Expense Breakdown
**GET** `/reports/expense-breakdown`

Returns expenses grouped by category with percentages.

**Query Parameters:**
- `year`: optional, filter by year (default: current year)
- `month`: optional, filter by specific month (1-12)

**Response (200):**
```json
{
  "data": [
    {
      "category": "Gaji",
      "amount": 15000000,
      "count": 1,
      "percentage": 33.33
    },
    {
      "category": "Operasional",
      "amount": 12000000,
      "count": 8,
      "percentage": 26.67
    }
    // ... more categories
  ],
  "total_expenses": 45000000
}
```

### 4. Get Recent Transactions
**GET** `/reports/transactions`

Returns recent income and expense transactions.

**Query Parameters:**
- `limit`: optional, max number of transactions (default: 50)
- `year`: optional, filter by year (default: current year)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "date": "2025-10-12",
      "type": "income",
      "category": "Booking",
      "description": "Pembayaran booking - John Doe (Booking #BK-1)",
      "amount": 2500000
    },
    {
      "id": 2,
      "date": "2025-10-12",
      "type": "expense",
      "category": "Operasional",
      "description": "Pembelian perlengkapan kamar mandi",
      "amount": 750000
    }
    // ... more transactions
  ]
}
```

### 5. Get Income Performance
**GET** `/reports/income-performance`

Returns income performance metrics and statistics.

**Query Parameters:**
- `year`: optional, filter by year (default: current year)

**Response (200):**
```json
{
  "data": {
    "average_per_month": 110500000,
    "best_month": {
      "month": "Oktober",
      "month_number": 10,
      "amount": 125000000
    },
    "worst_month": {
      "month": "Februari",
      "month_number": 2,
      "amount": 88000000
    },
    "total_bookings": 156,
    "total_income": 1326000000
  }
}
```

### 6. Get Expense Performance
**GET** `/reports/expense-performance`

Returns expense performance metrics and efficiency status.

**Query Parameters:**
- `year`: optional, filter by year (default: current year)

**Response (200):**
```json
{
  "data": {
    "average_per_month": 40300000,
    "highest_month": {
      "month": "Oktober",
      "month_number": 10,
      "amount": 45000000
    },
    "lowest_month": {
      "month": "Februari",
      "month_number": 2,
      "amount": 32000000
    },
    "efficiency": "Baik",
    "total_expenses": 483600000
  }
}
```

## Frontend Integration Example

```typescript
import axios from 'axios';

const API_BASE_URL = 'http://127.0.0.1:8000/api/owner/reports';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Add token to every request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Get financial summary
export const getFinancialSummary = async (params?: {
  year?: string;
  month?: number;
  start_date?: string;
  end_date?: string;
}) => {
  const response = await api.get('/financial-summary', { params });
  return response.data.data;
};

// Get monthly trend
export const getMonthlyTrend = async (year?: string) => {
  const response = await api.get('/monthly-trend', { 
    params: { year } 
  });
  return response.data.data;
};

// Get expense breakdown
export const getExpenseBreakdown = async (params?: {
  year?: string;
  month?: number;
}) => {
  const response = await api.get('/expense-breakdown', { params });
  return response.data;
};

// Get recent transactions
export const getRecentTransactions = async (params?: {
  limit?: number;
  year?: string;
}) => {
  const response = await api.get('/transactions', { params });
  return response.data.data;
};

// Get income performance
export const getIncomePerformance = async (year?: string) => {
  const response = await api.get('/income-performance', { 
    params: { year } 
  });
  return response.data.data;
};

// Get expense performance
export const getExpensePerformance = async (year?: string) => {
  const response = await api.get('/expense-performance', { 
    params: { year } 
  });
  return response.data.data;
};

// Complete usage example
const OwnerReportsPage = () => {
  const [loading, setLoading] = useState(true);
  const [summary, setSummary] = useState(null);
  const [monthlyData, setMonthlyData] = useState([]);
  const [selectedYear, setSelectedYear] = useState('2025');

  useEffect(() => {
    fetchFinancialData();
  }, [selectedYear]);

  const fetchFinancialData = async () => {
    try {
      setLoading(true);
      
      // Fetch all data in parallel
      const [summaryData, trendData, breakdownData, transactionsData, incomePerf, expensePerf] = await Promise.all([
        getFinancialSummary({ year: selectedYear }),
        getMonthlyTrend(selectedYear),
        getExpenseBreakdown({ year: selectedYear }),
        getRecentTransactions({ limit: 20, year: selectedYear }),
        getIncomePerformance(selectedYear),
        getExpensePerformance(selectedYear),
      ]);

      setSummary(summaryData);
      setMonthlyData(trendData);
      // ... set other state
      
    } catch (error) {
      console.error('Failed to fetch financial data:', error);
      toast.error('Gagal memuat data laporan keuangan');
    } finally {
      setLoading(false);
    }
  };

  // ... render component
};
```

## Error Responses

### 403 Forbidden
```json
{
  "message": "Anda bukan owner."
}
```

### 422 Validation Error
```json
{
  "message": "Validation failed",
  "errors": {
    "year": ["The year field must be a valid year."]
  }
}
```

## Notes

- All amount values are returned as floats (numbers)
- All dates are in `Y-m-d` format (YYYY-MM-DD)
- Growth percentages can be negative (indicating decline)
- Efficiency status values: "Baik", "Sedang", "Perlu Optimasi"
- Income includes bookings with status: confirmed, checked_in, checked_out
- Transactions are limited to 50 by default, use `limit` parameter to get more

## Testing with cURL

```bash
# Get financial summary for 2025
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     "http://127.0.0.1:8000/api/owner/reports/financial-summary?year=2025"

# Get monthly trend
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     "http://127.0.0.1:8000/api/owner/reports/monthly-trend?year=2025"

# Get expense breakdown for October 2025
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     "http://127.0.0.1:8000/api/owner/reports/expense-breakdown?year=2025&month=10"
```
