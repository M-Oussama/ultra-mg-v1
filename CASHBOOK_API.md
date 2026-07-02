# 💰 CASHBOOK & TRANSACTIONS API DOCUMENTATION

This documentation covers the newly implemented Cashbook and Transaction endpoints for financial tracking.

**Base URL**: `/api`  
**Authentication**: Required (Sanctum)  
**Header**: `Authorization: Bearer <token>`

---

## 1. Cashbooks Hub

### 📡 Get All Cashbooks
Returns a list of all cashbooks scoped to the current user or their organization.
- **Endpoint**: `GET /api/cashbooks`
- **Response**: `200 OK`
```json
[
  {
    "id": 1,
    "name": "General Ledger",
    "description": "Daily main operational funds",
    "balance": 15400.50,
    "transaction_count": 24,
    "last_transaction_date": "2024-04-24",
    "created_at": "2024-04-20T10:00:00Z"
  }
]
```

### 📡 Create Cashbook
- **Endpoint**: `POST /api/cashbooks`
- **Payload**:
| Field | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | Name of the ledger |
| `description` | string | No | Purpose of the ledger |

### 📡 Get Cashbook Detail & Summary
Returns full ledger info plus financial summary (Income/Expense/Balance).
- **Endpoint**: `GET /api/cashbooks/{id}`
- **Response**: `200 OK`
```json
{
  "cashbook": { "id": 1, "name": "General Ledger", ... },
  "summary": {
    "total_income": 20000.00,
    "total_expense": 4599.50,
    "balance": 15400.50,
    "transaction_count": 24
  },
  "recent_transactions": [ ... ]
}
```

### 📡 Delete Cashbook
- **Endpoint**: `DELETE /api/cashbooks/{id}`

---

## 2. Transactions

### 📡 List Transactions per Cashbook
- **Endpoint**: `GET /api/cashbooks/{cashbook_id}/transactions`
- **Ordering**: Descending by date and id.

### 📡 Add Transaction
Adds an income or expense record to a specific cashbook.
- **Endpoint**: `POST /api/cashbooks/{cashbook_id}/transactions`
- **Payload**:
| Field | Type | Required | Description |
|---|---|---|---|
| `type` | enum | Yes | `income` or `expense` |
| `amount` | decimal | Yes | Positive numeric value |
| `note` | string | No | Short description |
| `transaction_date` | date | Yes | Format: `YYYY-MM-DD` |
| `attachment` | file | No | Single image or PDF attachment |
| `attachments[]` | file[] | No | Multiple image or PDF attachments |

### 📡 Get Transaction Detail
Returns a single transaction with its attachments and related lookup data.
- **Endpoint**: `GET /api/transactions/{id}`

### 📡 Update Transaction
- **Endpoint**: `PUT /api/transactions/{id}`
- **Payload**: Same as Add Transaction.

### 📡 Delete Transaction
- **Endpoint**: `DELETE /api/transactions/{id}`

---

## 🔐 Security & Scoping Rules
1. **Ownership**: Users can only access objects linked to their `user_id`.
2. **Organization Access**: If a user is part of an organization (`organization_id` is set), they will see all data scoped to that organization, allowing for team collaboration.
3. **Validation**: Amounts must be positive. Types are strictly limited to `income` or `expense`.
