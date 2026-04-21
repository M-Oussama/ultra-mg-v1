# Sales Suppliers API Documentation

Endpoints for managing department-specific suppliers used in the sales departments.

## Base URL
`/api/sales-suppliers`

## Endpoints

### 1. List Sales Suppliers
Retrieves a paginated list of sales suppliers with optional filtering by department and search terms.

- **URL:** `/list`
- **Method:** `GET`
- **Query Parameters:**
    - `perPage` (Optional): Items per page (default: 10).
    - `currentPage` (Optional): Current page (default: 1).
    - `departement_id` (Optional): Filter by department ID.
    - `searchValue` (Optional): Search string (matches name, email, phone, etc.).
- **Response:**
    - `200 OK`: JSON object with suppliers, cities, departments, and pagination.
    ```json
    {
      "success": true,
      "suppliers": [...],
      "cities": [...],
      "departments": [...],
      "total": 50,
      "totalPage": 5
    }
    ```

### 2. Get Initialization Data
Fetches cities and departments list for create/edit forms.

- **URL:** `/getData`
- **Method:** `GET`
- **Response:**
    - `200 OK`:
    ```json
    {
      "cities": [...],
      "departments": [...]
    }
    ```

### 3. Create Sales Supplier
Creates a new sales supplier linked to a department.

- **URL:** `/create`
- **Method:** `POST`
- **Body Parameters:**
    - `name` (Required): `string`
    - `surname` (Optional): `string`
    - `full_name` (Optional): `string`
    - `email` (Optional): `string`
    - `address` (Optional): `text`
    - `phone` (Optional): `string`
    - `NRC` (Optional): `string`
    - `NIF` (Optional): `string`
    - `NART` (Optional): `string`
    - `NIS` (Optional): `string`
    - `city_id` (Required): `integer` (exists in `cities`)
    - `departement_id` (Required): `integer` (exists in `departments`)
- **Response:**
    - `200 OK`: `{"success": true, "message": "Sales Supplier created successfully", "supplier": {...}}`

### 4. Update Sales Supplier
Updates an existing sales supplier.

- **URL:** `/update/{id}`
- **Method:** `POST`
- **Response:**
    - `200 OK`: `{"success": true, "message": "Sales Supplier updated successfully", "supplier": {...}}`

### 5. Delete Sales Supplier
Soft deletes a sales supplier.

- **URL:** `/delete`
- **Method:** `POST`
- **Body:**
    ```json
    {
      "id": 12
    }
    ```
- **Response:**
    - `200 OK`: `{"success": true, "message": "Sales Supplier deleted successfully"}`
