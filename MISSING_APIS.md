# TODO — Missing APIs & Implementation Tasks
> Project: `invy` Flutter app + Laravel backend
> Date: 2026-04-19
> Based on: Flutter report + Laravel `api.php` audit

---

## Part 1 — Missing Routes in `api.php` (Laravel)

These routes are called by Flutter but **do not exist** in `api.php` yet.
They must be added to `routes/api.php` AND implemented in their controllers.

---

### 🔴 Route 1 — Cheque Status Check
```
GET /api/cheques/status/{chequeId}
```
- **Controller:** `App\Http\Controllers\ChequeController@getStatus`
- **Purpose:** Returns how much of a cheque's amount is already used by invoices
- **Used by Flutter:** Certify invoice form — cheque over-allocation validation
- **Returns:** `{ used_amount, remaining_balance, is_available }`

---

### 🔴 Route 2 — Cheque Status Check (Excluding One Invoice)
```
GET /api/cheques/status/{chequeId}/{excludeCommandId}
```
- **Controller:** `App\Http\Controllers\ChequeController@getStatusExcluding`
- **Purpose:** Same as Route 1 but excludes a specific invoice from the used amount calculation
- **Used by Flutter:** Edit mode — prevents false over-allocation warning when editing the invoice that already uses this cheque
- **Returns:** `{ used_amount, remaining_balance, is_available }`

---

### 🔴 Route 3 — Available Cheques List
```
GET /api/cheques/available
```
- **Controller:** `App\Http\Controllers\ChequeController@getAvailable`
- **Purpose:** Returns only cheques that still have a remaining balance > 0
- **Used by Flutter:** `ChequePickerSheet` — filters cheques available for selection
- **Returns:** Array of cheques with `remaining_balance`

---

### 🟡 Route 4 — Sub-Certify Invoice PDF Export (Single)
```
GET /api/pdf/sub-certify-invoice/{id}
```
- **Controller:** `App\Http\Controllers\PDFController@exportSubCertifyInvoice`
- **Purpose:** Generates and returns a PDF for a single sub-certified invoice
- **Used by Flutter:** Sub-certify details screen — download/share PDF button
- **Returns:** PDF file stream
- **Note:** Follow the same pattern as `exportCertifyInvoice`

---

### 🟡 Route 5 — Multi-Cheque PDF Export
```
GET /api/pdf/multi-cheques?ids=1,2,3
```
- **Controller:** `App\Http\Controllers\PDFController@exportMultiCheques`
- **Purpose:** Generates a combined PDF for multiple selected cheques
- **Used by Flutter:** Cheque list screen — multi-select export (TODO feature)
- **Returns:** PDF file stream
- **Note:** Follow the same pattern as `exportMultiCertifyInvoices`

---

## Part 2 — Missing Logic in Existing Controllers (Laravel)

These routes already exist in `api.php` but the controller logic needs to be added or modified.

---

### 🔴 Fix 1 — Block Cheque Delete if Used by an Invoice
- **Route:** `DELETE /api/cheques/delete/{id}` ✅ (exists)
- **Controller:** `App\Http\Controllers\ChequeController@delete`
- **What to change:** Before deleting, check if the cheque is linked to any certify or sub-certify invoice
- **If linked:** Return `422` error:
  ```json
  { "message": "Cannot delete this cheque. It is used by one or more invoices." }
  ```
- **If not linked:** Proceed with deletion normally

---

### 🟡 Fix 2 — Descending Order on Certify Invoice List
- **Route:** `GET /api/certifyInvoices/list` ✅ (exists)
- **Controller:** `App\Http\Controllers\CertifyInvoiceController@getInvoices`
- **What to change:** Add `->orderBy('date', 'desc')` to the list query
- **Result:** Newest invoices appear first

---

### 🟡 Fix 3 — Descending Order on Sub-Certify Invoice List
- **Route:** `GET /api/sub-certify-invoices/list` ✅ (exists)
- **Controller:** `App\Http\Controllers\SubCertifyInvoiceController@getInvoices`
- **What to change:** Add `->orderBy('date', 'desc')` to the list query
- **Result:** Newest invoices appear first

---

## Part 3 — Flutter Tasks (No Laravel Changes Needed)

These use **existing APIs** that are already in `api.php` — Flutter just isn't calling them correctly yet.

---

### 🔴 Flutter Fix 1 — Sub-Certify Form: Full Cheque Validation
- **File:** `lib/features/client_invoicing/presentation/screens/sub_certify_invoice_form_screen.dart`
- **Problem:** Only has a plain text field for cheque number — no validation
- **What to do:**
  - Replace text field with the reusable `ChequePickerSheet` widget
  - Use `availableChequesProvider` → calls `GET /api/cheques/available` ✅
  - Show remaining balance after selection
  - On save, validate using `GET /api/cheques/status/{chequeId}` ✅
  - Match the same validation logic already in `certify_invoice_form_screen.dart`

---

### 🔴 Flutter Fix 2 — Sub-Certify Form: Auto-Copy Parent Invoice Items
- **File:** `lib/features/client_invoicing/presentation/screens/sub_certify_invoice_form_screen.dart`
- **Problem:** Parent invoice is selected but items are never copied from it
- **What to do:**
  - When user selects a parent invoice, call: `GET /api/certifyInvoices/getInvoice/{id}` ✅
  - Auto-fill invoice items from parent response
  - Apply `price × 1.25` to each item's unit price
  - Copy quantities as-is
  - Allow user to add/remove/edit items after auto-fill

---

## Full Summary

### Laravel — New Routes to Add to `api.php`

| # | Method | Route | Controller Method | Priority |
|---|---|---|---|---|
| 1 | GET | `/cheques/status/{chequeId}` | `ChequeController@getStatus` | 🔴 High |
| 2 | GET | `/cheques/status/{chequeId}/{excludeCommandId}` | `ChequeController@getStatusExcluding` | 🔴 High |
| 3 | GET | `/cheques/available` | `ChequeController@getAvailable` | 🔴 High |
| 4 | GET | `/pdf/sub-certify-invoice/{id}` | `PDFController@exportSubCertifyInvoice` | 🟡 Medium |
| 5 | GET | `/pdf/multi-cheques` | `PDFController@exportMultiCheques` | 🟡 Medium |

### Laravel — Existing Routes to Fix

| # | Method | Route | What to Fix | Priority |
|---|---|---|---|---|
| 6 | DELETE | `/cheques/delete/{id}` | Add pre-delete invoice usage check | 🔴 High |
| 7 | GET | `/certifyInvoices/list` | Add `orderBy('date', 'desc')` | 🟡 Medium |
| 8 | GET | `/sub-certify-invoices/list` | Add `orderBy('date', 'desc')` | 🟡 Medium |

### Flutter — Fixes Using Existing APIs

| # | File | What to Fix | Priority |
|---|---|---|---|
| 9 | `sub_certify_invoice_form_screen.dart` | Add full cheque validation with `ChequePickerSheet` | 🔴 High |
| 10 | `sub_certify_invoice_form_screen.dart` | Auto-copy parent invoice items with `price × 1.25` | 🔴 High |

---

## Status Tracker

| # | Task | Side | Status |
|---|---|---|---|
| 1 | `GET /cheques/status/{chequeId}` | Laravel | ⬜ Not started |
| 2 | `GET /cheques/status/{chequeId}/{excludeCommandId}` | Laravel | ⬜ Not started |
| 3 | `GET /cheques/available` | Laravel | ⬜ Not started |
| 4 | `GET /pdf/sub-certify-invoice/{id}` | Laravel | ⬜ Not started |
| 5 | `GET /pdf/multi-cheques` | Laravel | ⬜ Not started |
| 6 | Block cheque delete if used | Laravel | ⬜ Not started |
| 7 | Desc order — certify list | Laravel | ⬜ Not started |
| 8 | Desc order — sub-certify list | Laravel | ⬜ Not started |
| 9 | Sub-certify cheque validation | Flutter | ⬜ Not started |
| 10 | Sub-certify parent item auto-copy | Flutter | ⬜ Not started |