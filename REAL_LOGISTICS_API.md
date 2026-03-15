# API Documentation: Companies & Real Logistics Invoices

## Companies API

Base URL: `/api/companies`

### 1. List All Companies
- **Endpoint:** `GET /list`
- **Description:** Retrieves a list of all companies.
- **Response:**
  - `200 OK`: Array of company objects.

### 2. Get Company Details
- **Endpoint:** `GET /show/{id}`
- **Description:** Retrieves details of a specific company.
- **Parameters:**
  - `id` (path): The ID of the company.
- **Response:**
  - `200 OK`: Company object.
  - `404 Not Found`: If the company does not exist.

### 3. Create Company
- **Endpoint:** `POST /store`
- **Description:** Creates a new company record.
- **Body (JSON):**
  ```json
  {
    "name": "Company Name",
    "description": "Description text",
    "address": "Address Line 1",
    "address2": "Address Line 2 (optional)",
    "phone": "0123456789",
    "email": "info@company.com",
    "NRC": "NRC-123",
    "NIF": "NIF-456",
    "NART": "NART-789",
    "NIS": "NIS-012",
    "capitale": "1000000"
  }
  ```
- **Response:**
  - `201 Created`: Message and created company object.

### 4. Update Company
- **Endpoint:** `POST /update/{id}`
- **Description:** Updates an existing company record.
- **Parameters:**
  - `id` (path): The ID of the company.
- **Body (JSON):** (All fields are optional)
  ```json
  {
    "name": "Updated Name"
  }
  ```
- **Response:**
  - `200 OK`: Message and updated company object.

### 5. Delete Company
- **Endpoint:** `DELETE /delete/{id}`
- **Description:** Soft deletes a company record.
- **Response:**
  - `200 OK`: Success message.

---

## Real Logistics Invoices API

Base URL: `/api/real-logistics-invoices`

### 1. List Invoices
- **Endpoint:** `GET /list`
- **Description:** Retrieves a paginated list of real logistics invoices.
- **Query Parameters:**
  - `perPage`: Items per page (default: 10)
  - `currentPage`: Current page (default: 1)
  - `client_id`: Filter by client ID (optional)
  - `status`: Filter by status (optional)
  - `from`: Filter by start date (optional)
  - `to`: Filter by end date (optional)
- **Response:**
  - `200 OK`:
    ```json
    {
      "data": [...],
      "total": 100,
      "totalPage": 10
    }
    ```

### 2. Get Invoice Details
- **Endpoint:** `GET /show/{id}`
- **Description:** Retrieves details of a specific logistics invoice including items.
- **Response:**
  - `200 OK`: Invoice object with `client` and `items` relationships.

### 3. Store Invoice
- **Endpoint:** `POST /store`
- **Description:** Creates a new logistics invoice and its associated items.
- **Body (JSON):**
  ```json
  {
    "invoice_date": "2024-03-01",
    "client_id": 1,
    "notes": "Some notes",
    "items": [
      {
        "product_id": 5,
        "product_name": "Product Name (if product_id is null)",
        "quantity": 10,
        "price": 100.00
      }
    ]
  }
  ```
- **Note:** `product_name` is required if `product_id` is not provided.
- **Response:**
  - `201 Created`: Message and created invoice object.

### 4. Update Invoice
- **Endpoint:** `POST /update/{id}`
- **Description:** Updates an existing logistics invoice and replaces its items.
- **Body (JSON):**
  ```json
  {
    "invoice_date": "2024-03-02",
    "items": [
      {
        "product_name": "Custom Product",
        "quantity": 5,
        "price": 200.00
      }
    ]
  }
  ```
- **Response:**
  - `200 OK`: Message and updated invoice object.

### 5. Delete Invoice
- **Endpoint:** `DELETE /delete/{id}`
- **Description:** Deletes a logistics invoice and its items (soft delete).
- **Response:**
  - `200 OK`: Success message.

---

## PDF Generation

### 1. Preview PDF (From Request Data)
- **Endpoint:** `POST /api/real-logistics-invoices/preview-pdf`
- **Description:** Generates a PDF stream from raw invoice data without saving it.
- **Body:** Same as `POST /store`.
- **Response:** PDF Stream.

### 2. Export PDF (From Stored Data)
- **Endpoint:** `GET /api/real-logistics-invoices/export-pdf/{id}`
- **Description:** Generates a PDF stream for a saved invoice.
- **Response:** PDF Stream.
