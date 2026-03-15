# Next Invoice ID APIs

These utility endpoints return the next available `fac_id` (Invoice Number) for a given year. The `fac_id` is calculated as the maximum existing `fac_id` for that year plus one.

## 1. Get Next Certify Invoice ID
Returns the next available `fac_id` for the `certify_invoices` table.

- **URL:** `/api/certifyInvoices/getLastID`
- **Method:** `GET`
- **Query Parameters:**
    - `date` (required, format: `YYYY-MM-DD`) - Used to determine the year of the invoice.
- **Example Request:**
    `GET /api/certifyInvoices/getLastID?date=2026-02-14`
- **Response:**
    ```json
    {
        "id": 11
    }
    ```

---

## 2. Get Next Sub-Certify Invoice ID
Returns the next available `fac_id` for the `sub_certify_invoices` table.

- **URL:** `/api/sub-certify-invoices/getLastID`
- **Method:** `GET`
- **Query Parameters:**
    - `date` (required, format: `YYYY-MM-DD`) - Used to determine the year of the invoice.
- **Example Request:**
    `GET /api/sub-certify-invoices/getLastID?date=2026-02-14`
- **Response:**
    ```json
    {
        "id": 5
    }
    ```
