# Department System & API Documentation

This document describes the Department system, including the department management APIs and how other modules (Sales, Products, Clients) interact with it.

## 1. Department CRUD (Base URL: `/api/departments`)

### Get All Departments (No Pagination)
- **URL:** `/all`
- **Method:** `GET`
- **Response:**
  ```json
  {
      "success": true,
      "departments": [{"id": 1, "name": "blow molding", ...}, ...]
  }
  ```

### List Departments (With Pagination)
- **URL:** `/list`
- **Method:** `GET`
- **Parameters:** `perPage`, `currentPage`

### Create Department
- **URL:** `/create`
- **Method:** `POST`
- **Body:** `{ "name": "New Dept" }`

### Update Department
- **URL:** `/update`
- **Method:** `POST`
- **Body:** `{ "id": 1, "name": "Updated Name" }`

### Delete Department
- **URL:** `/delete`
- **Method:** `POST`
- **Body:** `{ "id": 1 }`
- **Note:** Cannot be deleted if linked to Sales, Products, or Clients.

---

## 2. Integrated Modules (Sales, Products, Clients)

All following modules now support a `department_id` field. If not provided during creation/update, it defaults to `1`.

### Clients
- **List:** `GET /api/clients/list?department_id=1`
- **Store:** `POST /api/clients/store` (Accepts `department_id`)
- **Update:** `POST /api/clients/update/{id}` (Accepts `department_id`)

### Products
- **List:** `GET /api/products/list?department_id=1`
- **Store:** `POST /api/products/store` (Accepts `department_id`)
- **Update:** `POST /api/products/update/{id}` (Accepts `department_id`)

### Sales (POS)
- **List:** `GET /api/pos/sales/list?department_id=1`
- **Store:** `POST /api/pos/sales/store` 
  - Send `department_id` inside the `data` object.
- **Update:** `POST /api/pos/sales/update/{id}`
  - Send `department_id` inside the `data` object.

---

## 3. Global Behavior
- **Optionality:** `department_id` is always optional in requests and defaults to department `1`.
- **Filtering:** All major listing APIs support filtering by passing `department_id` as a query parameter.
