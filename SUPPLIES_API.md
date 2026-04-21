# Supplies API Documentation (Buying Invoices)

Endpoints for managing buying invoices (supplies) for sales departments. This module follows the same logical structure as Sales but uses `Sales Suppliers` and relates to departments.

## Base URL
`/api/supplies`

## Endpoints

### 1. List Supplies
Retrieves a paginated list of supplies with optional filtering.

- **URL:** `/list`
- **Method:** `GET`
- **Query Parameters:**
    - `perPage` (Optional): Items per page (default: 10).
    - `currentPage` (Optional): Current page (default: 1).
    - `supplier_id` (Optional): Filter by sales supplier ID.
    - `departement_id` (Optional): Filter by department ID.
    - `from` (Optional): Start date (`YYYY-MM-DD`).
    - `to` (Optional): End date (`YYYY-MM-DD`).
- **Response:**
    - `200 OK`: JSON object with supplies list and pagination.
    ```json
    {
      "supplies": [...],
      "currentPage": 1,
      "totalPage": 10,
      "totalSupplies": 100
    }
    ```

### 2. Get Initialization Data
Fetches necessary data (suppliers, departments, products, cities) for the create/edit forms.

- **URL:** `/getData`
- **Method:** `GET`
- **Response:**
    - `200 OK`:
    ```json
    {
      "suppliers": [...],
      "departments": [...],
      "products": [...],
      "cities": [...]
    }
    ```

### 3. Create Supply
Creates a new buying invoice and its associated items.

- **URL:** `/store`
- **Method:** `POST`
- **Body (`data` object):**
    ```json
    {
      "data": {
        "supply_date": "2024-03-15",
        "supplier": { "id": 1 },
        "departement_id": 2,
        "total_amount": 5000.00,
        "notes": "Payment on delivery",
        "supply_items": [
          {
            "product": { "id": 10 },
            "quantity": 100,
            "unit_price": 50.00
          }
        ]
      }
    }
    ```
- **Response:**
    - `200 OK`: `{"success": true, "message": "Supply created successfully", "id": 12}`

### 4. Update Supply
Updates an existing supply and refreshes its items.

- **URL:** `/update/{id}`
- **Method:** `POST`
- **Response:**
    - `200 OK`: `{"success": true, "message": "Supply updated successfully", "id": 12}`

### 5. Show Supply
Retrieves details of a specific supply including its items.

- **URL:** `/show/{id}`
- **Method:** `GET`
- **Response:**
    - `200 OK`: `{"supply": {...}}`

### 6. Delete Supply
Deletes a supply and its items.

- **URL:** `/delete`
- **Method:** `POST`
- **Body:**
    ```json
    {
      "id": 12
    }
    ```
- **Response:**
    - `200 OK`: `{"success": true, "message": "Supply deleted successfully"}`
