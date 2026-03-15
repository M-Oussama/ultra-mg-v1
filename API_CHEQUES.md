# Cheques API Documentation

Endpoints for managing cheques associated with certified clients.

## Base URL
`/api/cheques`

## Endpoints

### 1. List All Cheques
Retrieves a list of all cheques with their associated client information.

- **URL:** `/list`
- **Method:** `GET`
- **Response:**
    - `200 OK`: JSON array of cheque objects.
    ```json
    [
      {
        "id": 1,
        "cheque_date": "2024-02-12",
        "cheque_number": "CHQ-001",
        "client_id": 1,
        "file_path": "cheques/scans/file.pdf",
        "client": { ... }
      }
    ]
    ```

### 2. Store New Cheque
Creates a new cheque and allows uploading a PDF scan.

- **URL:** `/store`
- **Method:** `POST`
- **Content-Type:** `multipart/form-data`
- **Parameters:**
    - `cheque_date` (Required): `date`
    - `cheque_number` (Required): `string`
    - `client_id` (Required): `integer` (must exist in `certify_clients`)
    - `file` (Optional): `file` (PDF only, max 10MB)
- **Response:**
    - `201 Created`: The created cheque object.
    - `422 Unprocessable Entity`: Validation errors.

### 3. Update Cheque
Updates an existing cheque's details and/or its PDF scan.

- **URL:** `/update/{id}`
- **Method:** `POST` (Use POST for multipart data)
- **Content-Type:** `multipart/form-data`
- **Parameters:**
    - `cheque_date` (Optional): `date`
    - `cheque_number` (Optional): `string`
    - `client_id` (Optional): `integer`
    - `file` (Optional): `file` (PDF only, max 10MB)
- **Response:**
    - `200 OK`: The updated cheque object.
    - `404 Not Found`: Cheque not found.

### 4. Delete Cheque
Soft deletes a cheque.

- **URL:** `/delete/{id}`
- **Method:** `DELETE`
- **Response:**
    - `200 OK`: `{"message": "Cheque deleted successfully"}`
    - `404 Not Found`: Cheque not found.
