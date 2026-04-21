# Invoicing API Documentation

This document provides technical details for the newly implemented and enhanced API endpoints within the Invoicing and Cheque Management systems.

---

## 💳 Cheque Management System

### 1. List Cheques
Retrieves a paginated list of all cheques, ordered by date in descending order (newest first).

- **Endpoint:** `GET /api/cheques/list`
- **Query Params:**
    - `page` (int, default: 1)
    - `perPage` (int, default: 15)
- **Response:** Paginated JSON objects containing cheque details including `amount`, `remaining_balance`, and `status`.

### 2. Available Cheques
Retrieves cheques with a positive remaining balance for a specific client.

- **Endpoint:** `GET /api/cheques/available`
- **Query Params:** `client_id` (optional fallback: returns all tracked cheques with balance)
- **Response:** List of cheques with `remaining_balance` and `banque` (e.g., "CPA", "BDL", "SGT").

### 3. Check Cheque Allocation Status
Returns how much of a cheque's amount is already used by invoices.

- **Endpoint:** `GET /api/cheques/status/{chequeId}`
- **Alternative:** `GET /api/cheques/status/{chequeId}/{excludeCommandId}?type=certify|sub`
- **Purpose:** Prevents over-allocation. The alternative version excludes a specific invoice being edited.
- **Response:** `{ "used_amount", "remaining_balance", "is_available", "cheque_amount" }`

### 4. Create/Update Cheque
- **Endpoint:** `POST /api/cheques/store` or `POST /api/cheques/update/{id}`
- **Fields:**
    - `cheque_number` (string)
    - `cheque_date` (date)
    - `client_id` (fk)
    - `amount` (double)
    - `banque` (string, abbreviation like CPA, BDL, SGT)
    - `file` (pdf scan, optional)

### 5. Multi-Cheque PDF Export
Generates a combined PDF for multiple selected cheques.

- **Endpoint:** `GET /api/pdf/multi-cheques?ids=1,2,3`
- **Returns:** PDF stream.

### 6. Unify Cheque Statuses
An administrative utility to recalculate and synchronize all cheque statuses based on their current `remaining_balance`.

- **Endpoint:** `GET /api/cheques/unify-status`
- **Logic:**
    - `Status: full` if balance == amount.
    - `Status: partial` if 0 < balance < amount.
    - `Status: consumed` if balance <= 0.
- **Response:** `{ "message": "Success", "processed": int }`

---

## 📄 Certify Invoice System

### 1. List Certify Invoices
Retrieves a paginated list of certify invoices, ordered by date desc.

- **Endpoint:** `GET /api/certifyInvoices/list`
- **Response:** Paginated list of parent (Certify) invoices.

### 2. Get Invoice Detail
Fetches full details for a certify invoice including all line items.

- **Endpoint:** `GET /api/certifyInvoices/getInvoice/{id}`
- **Response:** Detailed JSON with nested `items` array, includes `cheque_id` and financial breakdowns (`tva_rate`, `timbre_rate`, etc).

### 3. Store/Update Certify Invoice
- **Endpoint:** `POST /api/certifyInvoices/store` or `POST /api/certifyInvoices/update/{id}`
- **New Fields:**
    - `cheque_id` (fk, relates to cheques table)
    - `tva_rate`, `tva_amount`, `ht_amount`
    - `timbre_rate`, `timbre_amount`
    - `cheque_number` (string fallback)

### 4. Export PDF
Generates a professional PDF for the specified certify invoice.

- **Endpoint:** `GET /api/pdf/certify-invoice/{id}`
- **Format:** Binary file (PDF).
- **Features:** High-fidelity branding, itemized tables, and total HT/TVA summary.

---

## 📑 Sub-Certify Invoice System

### 1. List Sub-Certify Invoices
Retrieves invoices created under a parent certify invoice.

- **Endpoint:** `GET /api/sub-certify-invoices/list`
- **Response:** Paginated list containing sub-invoice data.

### 2. Export PDF (Sub-Certify)
Generates a professional PDF for the specified sub-certify invoice.

- **Endpoint:** `GET /api/pdf/sub-certify-invoice/{id}`
- **Response:** Binary file (PDF).

### 3. Delete Sub-Certify Invoice
Safely removes a sub-certify invoice without affecting the parent data.

- **Endpoint:** `DELETE /api/sub-certify-invoices/delete/{id}`
- **Response:** `{ "message": "Success" }`

---

## 🛠 Generic Utilities

### Client Selection
Provides clients registered for the invoicing system.

- **Endpoint:** `GET /api/certify-clients/list`

### Product Catalog
Accesses the product list with prices tailored for certified invoicing.

- **Endpoint:** `GET /api/certify-products/list`

---

## 🔒 Data Integrity Rules
- **Multiplier:** Sub-Certify line items should default to parent price × 1.25 (Client-side recommendation).
- **Allocations:** Total TTC sum of Certify + Sub-Certify invoices linked to a single cheque must NOT exceed the cheque amount.
