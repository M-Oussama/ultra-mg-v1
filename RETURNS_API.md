# Product Returns API Documentation

Endpoints for managing product returns from clients.

## Base URL
`/api/returns`

## Endpoints

### 1. List Returns
Retrieves a paginated list of product returns with optional filtering.

- **URL:** `/list`
- **Method:** `GET`
- **Query Parameters:**
    - `searchValue` (Optional): String for filtering.
    - `perPage` (Optional): Number of items per page (default: 10).
    - `currentPage` (Optional): Current page number (default: 1).
    - `client_id` (Optional): Filter by client ID.
    - `from` (Optional): Start date (`YYYY-MM-DD`).
    - `to` (Optional): End date (`YYYY-MM-DD`).
    - `status` (Optional): Filter by payment status (`paid`).
- **Response:**
    - `200 OK`: JSON object containing returns, clients, and pagination info.
    ```json
    {
      "returns": [...],
      "clients": [...],
      "totalPage": 5,
      "totalReturns": 48
    }
    ```

### 2. Get Initialization Data
Retrieves necessary data (clients, products, cities, and next return ID) to initialize the creation form.

- **URL:** `/getData`
- **Method:** `GET`
- **Response:**
    - `200 OK`:
    ```json
    {
      "clients": [...],
      "cities": [...],
      "products": [...],
      "sale_statues": [],
      "last_id": 124
    }
    ```

### 3. Store New Return
Creates a new product return and its associated items.

- **URL:** `/store`
- **Method:** `POST`
- **Body (`data` object):**
    ```json
    {
      "data": {
        "sale_date": "2024-03-15",
        "client": { "id": 1 },
        "total_amount": 1500.00,
        "payment": "unpaid",
        "sale_items": [
          {
            "product": { "id": 10 },
            "quantity": 2,
            "price": 750.00
          }
        ]
      }
    }
    ```
- **Response:**
    - `200 OK`: `{"message": "Product Return added successfully", "id": 124}`

### 4. Update Return
Updates an existing product return. This will replace the old return items with the new ones.

- **URL:** `/update/{id}`
- **Method:** `POST`
- **Body (`data` object):**
    ```json
    {
      "data": {
        "id": 124,
        "sale_date": "2024-03-16",
        "client": { "id": 1 },
        "total_amount": 1200.00,
        "paid": "paid",
        "sale_items": [...]
      }
    }
    ```
- **Response:**
    - `200 OK`: `{"message": "Return Products updated successfully", "id": 124}`

### 5. Get Return Data for Edit
Retrieves the details of a specific return to populate the edit form.

- **URL:** `/getReturnData/{id}`
- **Method:** `GET`
- **Response:**
    - `200 OK`:
    ```json
    {
      "product_return": {
        "id": 124,
        "sale_items": [...],
        ...
      },
      "cities": [...],
      "clients": [...],
      "products": [...],
      "sale_statues": []
    }
    ```

### 6. Get Return Details (View/Print)
Retrieves detailed information for a specific return, including company details and amount in letters.

- **URL:** `/getReturn/{id}`
- **Method:** `GET`
- **Response:**
    - `200 OK`:
    ```json
    {
      "sold": 0,
      "product_return": {
        "id": 124,
        "amount_letter": "One Thousand Two Hundred...",
        "sale_items": [...],
        ...
      },
      "companies": [...],
      "clients": [...]
    }
    ```

### 7. Delete Return
Deletes a product return and its associated items.

- **URL:** `/delete`
- **Method:** `POST`
- **Body:**
    ```json
    {
      "sale": {
        "id": 124
      }
    }
    ```
- **Response:**
    - `200 OK`: `"Return deleted Successfully"`
