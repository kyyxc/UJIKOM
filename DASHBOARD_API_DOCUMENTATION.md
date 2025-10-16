# Owner Dashboard API Documentation

Base URL: `http://127.0.0.1:8000/api/owner`

All endpoints require authentication with Bearer token in Authorization header.

## Endpoints

### 1. Get Dashboard Data
**GET** `/dashboard`

Returns comprehensive dashboard data including financial summary, recent expenses, and category breakdown for the current month.

**Response (200):**
```json
{
  "summary": {
    "total_income": 125000000,
    "total_expenses": 45000000,
    "net_profit": 80000000,
    "total_bookings": 156,
    "income_growth": 12.5,
    "expense_growth": 8.2,
    "profit_growth": 15.3
  },
  "recent_expenses": [
    {
      "id": 1,
      "category": "Operasional",
      "amount": 5000000,
      "description": "Pembelian perlengkapan hotel",
      "date": "2025-10-10"
    },
    {
      "id": 2,
      "category": "Gaji",
      "amount": 15000000,
      "description": "Gaji karyawan bulan Oktober",
      "date": "2025-10-05"
    }
  ],
  "category_breakdown": [
    {
      "category": "Gaji",
      "amount": 15000000,
      "percentage": 65.2
    },
    {
      "category": "Operasional",
      "amount": 5000000,
      "percentage": 21.7
    },
    {
      "category": "Maintenance",
      "amount": 3000000,
      "percentage": 13.1
    }
  ],
  "period": {
    "current_month": {
      "start": "2025-10-01",
      "end": "2025-10-31",
      "name": "Oktober 2025"
    },
    "previous_month": {
      "start": "2025-09-01",
      "end": "2025-09-30",
      "name": "September 2025"
    }
  }
}
```

### 2. Get Quick Stats
**GET** `/dashboard/quick-stats`

Returns quick statistics for today and this week.

**Response (200):**
```json
{
  "today": {
    "income": 5000000,
    "bookings": 3,
    "expenses": 1500000,
    "profit": 3500000
  },
  "this_week": {
    "income": 25000000,
    "expenses": 8000000,
    "profit": 17000000
  }
}
```

## Data Descriptions

### Summary Object
- `total_income`: Total pendapatan bulan ini (dari bookings dengan status confirmed, checked_in, checked_out)
- `total_expenses`: Total pengeluaran bulan ini
- `net_profit`: Keuntungan bersih (income - expenses)
- `total_bookings`: Jumlah reservasi bulan ini
- `income_growth`: Persentase pertumbuhan income dibanding bulan lalu
- `expense_growth`: Persentase pertumbuhan expense dibanding bulan lalu
- `profit_growth`: Persentase pertumbuhan profit dibanding bulan lalu

### Recent Expenses Array
- Limited to 5 most recent expenses
- Sorted by date descending
- Only includes current month expenses

### Category Breakdown Array
- Expenses grouped by category
- Includes percentage of total expenses
- Sorted by amount descending
- Only includes current month expenses

## Frontend Integration Example

### React/TypeScript Implementation

```typescript
import axios from 'axios';
import { useEffect, useState } from 'react';

const API_BASE_URL = 'http://127.0.0.1:8000/api/owner';

interface DashboardData {
  summary: {
    total_income: number;
    total_expenses: number;
    net_profit: number;
    total_bookings: number;
    income_growth: number;
    expense_growth: number;
    profit_growth: number;
  };
  recent_expenses: Array<{
    id: number;
    category: string;
    amount: number;
    description: string;
    date: string;
  }>;
  category_breakdown: Array<{
    category: string;
    amount: number;
    percentage: number;
  }>;
}

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Add token interceptor
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Fetch dashboard data
export const getDashboardData = async (): Promise<DashboardData> => {
  const response = await api.get('/dashboard');
  return response.data;
};

// Fetch quick stats
export const getQuickStats = async () => {
  const response = await api.get('/dashboard/quick-stats');
  return response.data;
};

// Usage in component
const OwnerDashboardPage = () => {
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const dashboardData = await getDashboardData();
        setData(dashboardData);
      } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  // Render your dashboard UI
  return (
    <div>
      {loading ? (
        <SkeletonLoader />
      ) : (
        <>
          <SummaryCards summary={data?.summary} />
          <RecentExpenses expenses={data?.recent_expenses} />
          <CategoryBreakdown categories={data?.category_breakdown} />
        </>
      )}
    </div>
  );
};
```

### Update Your Frontend Code

Replace this line in your `OwnerDashboardPage`:

```typescript
// ❌ OLD - Wrong endpoint
const summaryRes = await axios.get(
  "http://127.0.0.1:8000/api/owner/reports/finance/summary",
  { headers: { Authorization: `Bearer ${token}` } }
);

// ✅ NEW - Correct endpoint
const dashboardRes = await axios.get(
  "http://127.0.0.1:8000/api/owner/dashboard",
  { headers: { Authorization: `Bearer ${token}` } }
);

// Use the response data
setSummary(dashboardRes.data.summary);
setRecentExpenses(dashboardRes.data.recent_expenses);
setCategoryBreakdown(dashboardRes.data.category_breakdown);
```

### Complete Frontend Update

```typescript
useEffect(() => {
  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      const token = localStorage.getItem("token");

      // Fetch dashboard data from single endpoint
      const { data } = await axios.get(
        "http://127.0.0.1:8000/api/owner/dashboard",
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );

      setSummary(data.summary);
      setRecentExpenses(data.recent_expenses);
      setCategoryBreakdown(data.category_breakdown);
    } catch (err) {
      console.error("Failed to fetch dashboard data", err);
      toast.error("Gagal memuat data dashboard");
    } finally {
      setLoading(false);
    }
  };

  fetchDashboardData();
}, []);
```

## Error Responses

### 403 Forbidden
```json
{
  "message": "Anda bukan owner"
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

## Testing with cURL

```bash
# Get dashboard data
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     "http://127.0.0.1:8000/api/owner/dashboard"

# Get quick stats
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     "http://127.0.0.1:8000/api/owner/dashboard/quick-stats"
```

## Notes

- Dashboard data is calculated for the current month
- Growth percentages compare current month vs previous month
- Recent expenses limited to 5 items
- Category breakdown shows percentage of total expenses
- All amount values are returned as floats
- Dates are in ISO format (YYYY-MM-DD)
- Period information includes both current and previous month for context

## Related Endpoints

For more detailed financial reports, use:
- `/api/owner/reports/financial-summary` - Detailed financial summary with filters
- `/api/owner/reports/monthly-trend` - 12-month trend data
- `/api/owner/expenses` - Full expense management

See `REPORTS_API_DOCUMENTATION.md` and `EXPENSE_API_DOCUMENTATION.md` for more details.
