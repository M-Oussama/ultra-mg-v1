# SubCertifyInvoices CRUD API Documentation

This document outlines the API endpoints for managing `SubCertifyInvoices` and their associated products.

## base URL: `/api/sub-certify-invoices`

### 1. List Sub Invoices
Returns a paginated list of sub invoices.

- **URL:** `/list`
- **Method:** `GET`
- **Parameters:**
    - `perPage` (optional, default: 10)
    - `currentPage` (optional, default: 1)
- **Response:**
    ```json
    {
        "invoices": { ... paginated data ... },
        "totalPage": 1,
        "totalInvoices": 5
    }
    ```

### 2. Get Single Sub Invoice
Returns detailed data for a specific sub invoice, including clients and products for editing.

- **URL:** `/getInvoice/{id}`
- **Method:** `GET`
- **Response:**
    ```json
    {
        "invoice": { ... invoice data with products ... },
        "clients": [ ... ],
        "products": [ ... ],
        "unSelectedProducts": [ ... ]
    }
    ```

### 3. Store Sub Invoice
Creates a new sub invoice and its associated products.

- **URL:** `/store`
- **Method:** `POST`
- **Request Body:**
    ```json
    {
        "invoiceData": {
            "certify_invoice_id": 1,
            "fac_id": 1,
            "date": "2023-08-13",
            "client": { "id": 1 },
            "amount": 100.0,
            "payment_type": "cash",
            "tva_rate": 19,
            "tva_amount": 19,
            "ht_amount": 100,
            "timbre_rate": null,
            "timbre_amount": 1,
            "cheque_number": "CHQ-123",
            "certify_invoice_products": [
                {
                    "product": { "id": 1 },
                    "price": 50,
                    "quantity": 2
                }
            ]
        }
    }
    ```
- **Response:**
    ```json
    {
        "message": "Sub Invoice created successfully",
        "id": 1
    }
    ```

### 4. Update Sub Invoice
Updates an existing sub invoice and replaces its products.

- **URL:** `/update/{id}`
- **Method:** `POST`
- **Request Body:** Same as Store, but includes `id` in `invoiceData`.
- **Response:**
    ```json
    {
        "message": "Sub Invoice Updated Successfully"
    }
    ```

### 5. Delete Sub Invoice
Deletes a sub invoice and its associated products.

- **URL:** `/delete/{id}`
- **Method:** `DELETE`
- **Response:**
    ```json
    {
        "message": "Sub Certify Invoice deleted successfully"
    }
    ```

### 6. Get Last ID
Returns the next available `fac_id` for a given date (year).

- **URL:** `/getLastID`
- **Method:** `GET`
- **Parameters:**
    - `date` (format: YYYY-MM-DD)
- **Response:**
    ```json
    {
        "id": 2
    }
    ```

### 7. Get Initialization Data
Returns data needed to initialize a new invoice (clients, products, next ID).

- **URL:** `/getInvoiceData`
- **Method:** `GET`
- **Response:**
    ```json
    {
        "clients": [ ... ],
        "products": [ ... ],
        "id": 2,
        "date": "2023-08-13"
    }
    ```
