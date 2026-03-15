# Importation Payments API Documentation

This API allows managing payments related to importation invoices, categorized by type and supporting multiple document uploads through Spatie Media Library.

## Endpoints

### 1. List Payments
- **URL**: `/api/importation-payments/list`
- **Method**: `GET`
- **Description**: Retrieves a list of all importation payments, including the associated invoice and links to the uploaded PDF files.
- **Success Response**: `200 OK` with a JSON array.

### 2. Store Payment
- **URL**: `/api/importation-payments/store`
- **Method**: `POST`
- **Content-Type**: `multipart/form-data`
- **Body Parameters**:
    - `importation_invoice_id` (Integer, Required): ID of the importation invoice.
    - `amount` (Number, Required): Payment amount.
    - `type` (String, Required): One of: `invoice_settlement`, `customs fee`, `freight/transit`.
    - `payment_date` (Date, Required): Date of payment (YYYY-MM-DD).
    - `notes` (String, Optional): Additional notes.
    - `files[]` (Array of Files, Optional): One or more PDF scans of the payment documents.
- **Success Response**: `201 Created`.

### 3. Update Payment
- **URL**: `/api/importation-payments/update/{id}`
- **Method**: `POST`
- **Content-Type**: `multipart/form-data`
- **Description**: Updates an existing payment. New files sent in `files[]` will be **appended** to the record.
- **Success Response**: `200 OK`.

### 4. Delete Payment
- **URL**: `/api/importation-payments/delete/{id}`
- **Method**: `DELETE`
- **Description**: Deletes a payment and all its associated media files from storage.
- **Success Response**: `200 OK`.

## Media Uploads (Spatie)
Payments support multiple PDF attachments. The API automatically handles storage and retrieval via Spatie Media Library. Ensure the disk is configured correctly in `config/medialibrary.php` (default is `public`).
