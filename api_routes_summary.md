# API Routes Detailed Documentation

## Documentation

### `GET` /documentation
- **Action:** `\L5Swagger\Http\Controllers\SwaggerController@api`
- **Name:** `l5-swagger.default.api`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Response

## Oauth2-callback

### `GET` /oauth2-callback
- **Action:** `\L5Swagger\Http\Controllers\SwaggerController@oauth2Callback`
- **Name:** `l5-swagger.default.oauth2_callback`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

## Users

### `GET` /users/list
- **Action:** `App\Http\Controllers\UserController@getUsers`
- **Name:** `getUsers`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /users/store
- **Action:** `App\Http\Controllers\UserController@store`
- **Name:** `storeUser`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `email`
  - `password`
  - `role`
- **Returns:** JSON Object / Array

### `POST` /users/update/{id}
- **Action:** `App\Http\Controllers\UserController@update`
- **Name:** `updateUser`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
  - `email`
  - `role_id`
- **Returns:** JSON Object / Array

### `DELETE` /users/delete/{id}
- **Action:** `App\Http\Controllers\UserController@delete`
- **Name:** `deleteUser`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Clients

### `GET` /clients/list
- **Action:** `App\Http\Controllers\ClientController@getClients`
- **Name:** `getClients`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /clients/store
- **Action:** `App\Http\Controllers\ClientController@store`
- **Name:** `store`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `city_id`
  - `surname`
  - `phone`
  - `address`
  - `NRC`
  - `NIF`
  - `NART`
  - `NIS`
  - `email`
  - `department_id`
- **Returns:** JSON Object / Array

### `POST` /clients/update/{id}
- **Action:** `App\Http\Controllers\ClientController@update`
- **Name:** `update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
  - `surname`
  - `phone`
  - `address`
  - `NRC`
  - `NIF`
  - `NART`
  - `NIS`
  - `email`
  - `department_id`
- **Returns:** JSON Object / Array

### `DELETE` /clients/delete/{id}
- **Action:** `App\Http\Controllers\ClientController@delete`
- **Name:** `delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /clients/getClientsPerCity/{id}
- **Action:** `App\Http\Controllers\ClientController@getClientsPerCity`
- **Name:** `getClientsPerCity`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /clients/log
- **Action:** `App\Http\Controllers\ClientLogController@getLog`
- **Name:** `getLog`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /clients/log/generate
- **Action:** `App\Http\Controllers\ClientLogController@getALLLog`
- **Name:** `getLog`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

## Products

### `GET` /products/list
- **Action:** `App\Http\Controllers\ProductController@getProducts`
- **Name:** `getProducts`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /products/store
- **Action:** `App\Http\Controllers\ProductController@store`
- **Name:** `store`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `brand`
  - `description`
  - `product_code`
  - `category_id`
  - `SKU`
  - `min_stock_level`
  - `price`
  - `stockable`
  - `tax_rate`
  - `weight`
  - `department_id`
- **Returns:** JSON Object / Array

### `POST` /products/update/{id}
- **Action:** `App\Http\Controllers\ProductController@update`
- **Name:** `update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
  - `brand`
  - `description`
  - `product_code`
  - `category_id`
  - `SKU`
  - `min_stock_level`
  - `price`
  - `stockable`
  - `tax_rate`
  - `type_id`
  - `weight`
  - `department_id`
- **Returns:** JSON Object / Array

### `DELETE` /products/delete/{id}
- **Action:** `App\Http\Controllers\ProductController@delete`
- **Name:** `delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## CertifyInvoices

### `GET` /certifyInvoices/list
- **Action:** `App\Http\Controllers\CertifyInvoiceController@getInvoices`
- **Name:** `getInvoices`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /certifyInvoices/getInvoice/{id}
- **Action:** `App\Http\Controllers\CertifyInvoiceController@getInvoice`
- **Name:** `getInvoice`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /certifyInvoices/store
- **Action:** `App\Http\Controllers\CertifyInvoiceController@store`
- **Name:** `store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /certifyInvoices/update/{id}
- **Action:** `App\Http\Controllers\CertifyInvoiceController@update`
- **Name:** `update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `DELETE` /certifyInvoices/delete/{id}
- **Action:** `App\Http\Controllers\CertifyInvoiceController@delete`
- **Name:** `deleteCertifyInvoice`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /certifyInvoices/getInvoiceData
- **Action:** `App\Http\Controllers\CertifyInvoiceController@getData`
- **Name:** `getData`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Response

### `GET` /certifyInvoices/getLastID
- **Action:** `App\Http\Controllers\CertifyInvoiceController@getLastID`
- **Name:** `getLastID`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Certify-clients

### `GET` /certify-clients/list
- **Action:** `App\Http\Controllers\CertifyClientController@getClients`
- **Name:** `getCertifyClients`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /certify-clients/store
- **Action:** `App\Http\Controllers\CertifyClientController@store`
- **Name:** `storeCertifyClient`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `city_id`
  - `surname`
  - `phone`
  - `address`
  - `NRC`
  - `NIF`
  - `NART`
  - `NIS`
  - `email`
- **Returns:** JSON Object / Array

### `POST` /certify-clients/update/{id}
- **Action:** `App\Http\Controllers\CertifyClientController@update`
- **Name:** `updateCertifyClient`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
  - `surname`
  - `phone`
  - `address`
  - `NRC`
  - `NIF`
  - `NART`
  - `NIS`
  - `email`
- **Returns:** JSON Object / Array

### `DELETE` /certify-clients/delete/{id}
- **Action:** `App\Http\Controllers\CertifyClientController@delete`
- **Name:** `deleteCertifyClient`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Certify-products

### `GET` /certify-products/list
- **Action:** `App\Http\Controllers\CertifyProductController@getProducts`
- **Name:** `getCertifyProducts`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /certify-products/store
- **Action:** `App\Http\Controllers\CertifyProductController@store`
- **Name:** `storeCertifyProduct`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `brand`
  - `description`
  - `product_code`
  - `category_id`
  - `SKU`
  - `min_stock_level`
  - `price`
  - `weight`
  - `stockable`
  - `tax_rate`
- **Returns:** JSON Object / Array

### `POST` /certify-products/update/{id}
- **Action:** `App\Http\Controllers\CertifyProductController@update`
- **Name:** `updateCertifyProduct`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
  - `brand`
  - `description`
  - `product_code`
  - `category_id`
  - `SKU`
  - `min_stock_level`
  - `price`
  - `weight`
  - `stockable`
  - `tax_rate`
- **Returns:** JSON Object / Array

### `DELETE` /certify-products/delete/{id}
- **Action:** `App\Http\Controllers\CertifyProductController@delete`
- **Name:** `deleteCertifyProduct`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Cheques

### `GET` /cheques/list
- **Action:** `App\Http\Controllers\ChequeController@getCheques`
- **Name:** `getCheques`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /cheques/store
- **Action:** `App\Http\Controllers\ChequeController@store`
- **Name:** `storeCheque`
- **Path Parameters:** None
- **Body Parameters:**
  - `only`
  - `hasFile`
- **Returns:** JSON Object / Array

### `POST` /cheques/update/{id}
- **Action:** `App\Http\Controllers\ChequeController@update`
- **Name:** `updateCheque`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `only`
  - `hasFile`
- **Returns:** JSON Object / Array

### `DELETE` /cheques/delete/{id}
- **Action:** `App\Http\Controllers\ChequeController@delete`
- **Name:** `deleteCheque`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Pos

### `GET` /pos/sales/list
- **Action:** `App\Http\Controllers\POSController@getSales`
- **Name:** `getSales`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /pos/sales/getPriceHistory/{clientId}/{productId}
- **Action:** `App\Http\Controllers\POSController@getPriceHistory`
- **Name:** `getPriceHistory`
- **Path Parameters:** {clientId}, {productId}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /pos/sales/getData
- **Action:** `App\Http\Controllers\POSController@getData`
- **Name:** `getData`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /pos/sales/store
- **Action:** `App\Http\Controllers\POSController@store`
- **Name:** `store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /pos/sales/delete
- **Action:** `App\Http\Controllers\POSController@deleteSale`
- **Name:** `deleteSale`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /pos/sale/getSale/{id}
- **Action:** `App\Http\Controllers\POSController@getSale`
- **Name:** `getSale`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /pos/sale/getSaleData/{id}
- **Action:** `App\Http\Controllers\POSController@getSaleData`
- **Name:** `getSale`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /pos/sales/update/{id}
- **Action:** `App\Http\Controllers\POSController@update`
- **Name:** `update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /pos/sales/payment/create/{id}
- **Action:** `App\Http\Controllers\POSController@addPayment`
- **Name:** `addPayment`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /pos/sales/payments/list
- **Action:** `App\Http\Controllers\POSController@listPayment`
- **Name:** `listPayment`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /pos/sales/payments/invoice/{sale_id}
- **Action:** `App\Http\Controllers\POSController@getSalePaymentsTotal`
- **Path Parameters:** {sale_id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /pos/sales/payment/create
- **Action:** `App\Http\Controllers\POSController@createPayment`
- **Name:** `createPayment`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /pos/sales/payment/update
- **Action:** `App\Http\Controllers\POSController@updatePayment`
- **Name:** `updatePayment`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /pos/sales/payment/delete
- **Action:** `App\Http\Controllers\POSController@deletePayment`
- **Name:** `deletePayment`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /pos/benefits/list
- **Action:** `App\Http\Controllers\BenefitController@getBenefits`
- **Name:** `getBenefits`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /pos/benefits/store
- **Action:** `App\Http\Controllers\BenefitController@store`
- **Name:** `store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /pos/benefits/{id}
- **Action:** `App\Http\Controllers\BenefitController@getArticlesBenefit`
- **Name:** `getArticlesBenefit`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `DELETE` /pos/benefits/delete/{id}
- **Action:** `App\Http\Controllers\BenefitController@destroyBenefit`
- **Name:** `destroyBenefit`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /pos/benefits/update/{id}
- **Action:** `App\Http\Controllers\BenefitController@updateBenefit`
- **Name:** `updateBenefit`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /pos/benefits/charges/update/{id}
- **Action:** `App\Http\Controllers\BenefitController@updateBenefitCharges`
- **Name:** `updateBenefitCharges`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /pos/benefits/refresh/{id}
- **Action:** `App\Http\Controllers\BenefitController@refreshBenefitData`
- **Name:** `refreshBenefitData`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /pos/client/{id}/sales
- **Action:** `App\Http\Controllers\POSController@getClientInvoices`
- **Name:** `getClientInvoices`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /pos/client/{id}/sales/{paymentId}/paid
- **Action:** `App\Http\Controllers\POSController@getPaidInvoices`
- **Name:** `getClientInvoices`
- **Path Parameters:** {id}, {paymentId}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Sales

### `POST` /sales/{sale}/toggle-pickup
- **Action:** `App\Http\Controllers\POSController@updatePickUp`
- **Name:** `updatePickUp`
- **Path Parameters:** {sale}
- **Body Parameters:**
  - `picked_up`
- **Returns:** JSON Object / Array

## Employees

### `GET` /employees/list
- **Action:** `App\Http\Controllers\EmployeeController@getEmployees`
- **Name:** `getEmployees`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /employees/store
- **Action:** `App\Http\Controllers\EmployeeController@store`
- **Name:** `store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /employees/update/{id}
- **Action:** `App\Http\Controllers\EmployeeController@update`
- **Name:** `update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `DELETE` /employees/delete/{id}
- **Action:** `App\Http\Controllers\EmployeeController@destroy`
- **Name:** `destroy`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /employees/{id}
- **Action:** `App\Http\Controllers\EmployeeController@getEmployee`
- **Name:** `getEmployee`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /employees/vacation/store/{id}
- **Action:** `App\Http\Controllers\VacationController@store`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Response

### `POST` /employees/vacation/update/{id}
- **Action:** `App\Http\Controllers\VacationController@update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /employees/vacation/list/{id}
- **Action:** `App\Http\Controllers\VacationController@getVacationsByEmployee`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /employees/vacation/list
- **Action:** `App\Http\Controllers\VacationController@getVacations`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `DELETE` /employees/vacation/delete/{id}
- **Action:** `App\Http\Controllers\VacationController@destroy`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /employees/vacation/{id}
- **Action:** `App\Http\Controllers\VacationController@getVacation`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Cities

### `GET` /cities/list
- **Action:** `App\Http\Controllers\EmployeeController@getCities`
- **Name:** `getCities`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Attendances

### `GET` /attendances/list
- **Action:** `App\Http\Controllers\AttendanceController@getAttendances`
- **Name:** `getAttendances`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /attendances/getAttendanceData/{id}
- **Action:** `App\Http\Controllers\AttendanceController@getAttendanceData`
- **Name:** `getAttendanceData`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /attendances/store
- **Action:** `App\Http\Controllers\AttendanceController@store`
- **Name:** `store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `POST` /attendances/submit
- **Action:** `App\Http\Controllers\AttendanceController@submit`
- **Name:** `submit`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /attendances/{id}
- **Action:** `App\Http\Controllers\AttendanceController@getAttendance`
- **Name:** `getAttendance`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /attendances/getAttendanceByID/{id}
- **Action:** `App\Http\Controllers\AttendanceController@getAttendanceByID`
- **Name:** `getAttendanceByID`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /attendances/edit/{id}
- **Action:** `App\Http\Controllers\AttendanceController@getAttendance`
- **Name:** `getAttendance`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /attendances/update
- **Action:** `App\Http\Controllers\AttendanceController@update`
- **Name:** `update`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /attendances/AddEmployeeToAttendance
- **Action:** `App\Http\Controllers\AttendanceController@AddEmployeeToAttendance`
- **Name:** `AddEmployeeToAttendance`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /attendances/RemoveEmployeeFromAttendance
- **Action:** `App\Http\Controllers\AttendanceController@RemoveEmployeeFromAttendance`
- **Name:** `RemoveEmployeeFromAttendance`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /attendances/employees/list/{id}
- **Action:** `App\Http\Controllers\AttendanceController@fetchEmployeesByAttendance`
- **Name:** `fetchEmployeesByAttendance`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /attendances/updateEndDate/{id}
- **Action:** `App\Http\Controllers\AttendanceController@updateEndDate`
- **Name:** `updateEndDate`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /attendances/addNewEmployeeAttendanceRecord/{id}
- **Action:** `App\Http\Controllers\AttendanceController@NewEmployeeAttendanceRecord`
- **Name:** `NewEmployeeAttendanceRecord`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /attendances/career/delete/{id}
- **Action:** `App\Http\Controllers\AttendanceController@deleteEmployeeCareer`
- **Name:** `deleteEmployeeCareer`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Suppliers

### `POST` /suppliers/list
- **Action:** `App\Http\Controllers\SupplierController@getSuppliers`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /suppliers/create
- **Action:** `App\Http\Controllers\SupplierController@create`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `POST` /suppliers/update
- **Action:** `App\Http\Controllers\SupplierController@update`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `POST` /suppliers/delete
- **Action:** `App\Http\Controllers\SupplierController@delete`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

## Sales-suppliers

### `GET` /sales-suppliers/list
- **Action:** `App\Http\Controllers\SalesSupplierController@getSuppliers`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /sales-suppliers/getData
- **Action:** `App\Http\Controllers\SalesSupplierController@getData`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /sales-suppliers/create
- **Action:** `App\Http\Controllers\SalesSupplierController@create`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /sales-suppliers/update/{id}
- **Action:** `App\Http\Controllers\SalesSupplierController@update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /sales-suppliers/delete
- **Action:** `App\Http\Controllers\SalesSupplierController@delete`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

## Departments

### `GET` /departments/all
- **Action:** `App\Http\Controllers\DepartmentController@index`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /departments/list
- **Action:** `App\Http\Controllers\DepartmentController@list`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /departments/create
- **Action:** `App\Http\Controllers\DepartmentController@create`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `POST` /departments/update
- **Action:** `App\Http\Controllers\DepartmentController@update`
- **Path Parameters:** None
- **Body Parameters:**
  - `id`
  - `name`
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `POST` /departments/delete
- **Action:** `App\Http\Controllers\DepartmentController@delete`
- **Path Parameters:** None
- **Body Parameters:**
  - `id`
- **Returns:** JSON Object / Array

## Supplies

### `GET` /supplies/list
- **Action:** `App\Http\Controllers\SupplyController@getSupplies`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /supplies/getData
- **Action:** `App\Http\Controllers\SupplyController@getData`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /supplies/store
- **Action:** `App\Http\Controllers\SupplyController@store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /supplies/update/{id}
- **Action:** `App\Http\Controllers\SupplyController@update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /supplies/show/{id}
- **Action:** `App\Http\Controllers\SupplyController@show`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /supplies/delete
- **Action:** `App\Http\Controllers\SupplyController@delete`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

## Importation-invoices

### `GET` /importation-invoices/list
- **Action:** `App\Http\Controllers\ImportationInvoiceController@list`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /importation-invoices/store
- **Action:** `App\Http\Controllers\ImportationInvoiceController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `only`
  - `hasFile`
- **Returns:** JSON Object / Array

### `POST` /importation-invoices/update/{id}
- **Action:** `App\Http\Controllers\ImportationInvoiceController@update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `only`
  - `has`
  - `hasFile`
- **Returns:** JSON Object / Array

### `DELETE` /importation-invoices/delete/{id}
- **Action:** `App\Http\Controllers\ImportationInvoiceController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Importation-payments

### `GET` /importation-payments/list/{invoice_id?}
- **Action:** `App\Http\Controllers\ImportationPaymentController@list`
- **Path Parameters:** {invoice_id?}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /importation-payments/store
- **Action:** `App\Http\Controllers\ImportationPaymentController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `only`
  - `hasFile`
- **Returns:** JSON Object / Array

### `POST` /importation-payments/update/{id}
- **Action:** `App\Http\Controllers\ImportationPaymentController@update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `only`
  - `hasFile`
- **Returns:** JSON Object / Array

### `DELETE` /importation-payments/delete/{id}
- **Action:** `App\Http\Controllers\ImportationPaymentController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Sub-certify-invoices

### `GET` /sub-certify-invoices/list
- **Action:** `App\Http\Controllers\SubCertifyInvoiceController@getInvoices`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /sub-certify-invoices/getInvoice/{id}
- **Action:** `App\Http\Controllers\SubCertifyInvoiceController@getInvoice`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /sub-certify-invoices/store
- **Action:** `App\Http\Controllers\SubCertifyInvoiceController@store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /sub-certify-invoices/update/{id}
- **Action:** `App\Http\Controllers\SubCertifyInvoiceController@update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `DELETE` /sub-certify-invoices/delete/{id}
- **Action:** `App\Http\Controllers\SubCertifyInvoiceController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /sub-certify-invoices/getInvoiceData
- **Action:** `App\Http\Controllers\SubCertifyInvoiceController@getInvoiceData`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /sub-certify-invoices/getLastID
- **Action:** `App\Http\Controllers\SubCertifyInvoiceController@getLastID`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Returns

### `GET` /returns/list
- **Action:** `App\Http\Controllers\ProductReturnController@getReturns`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /returns/store
- **Action:** `App\Http\Controllers\ProductReturnController@store`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /returns/getData
- **Action:** `App\Http\Controllers\ProductReturnController@getData`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /returns/delete
- **Action:** `App\Http\Controllers\ProductReturnController@deleteReturn`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `POST` /returns/update/{id}
- **Action:** `App\Http\Controllers\ProductReturnController@update`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** JSON Object / Array

### `GET` /returns/getReturnData/{id}
- **Action:** `App\Http\Controllers\ProductReturnController@getReturnData`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /returns/getReturn/{id}
- **Action:** `App\Http\Controllers\ProductReturnController@getReturn`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Recrutement

### `POST` /recrutement/generateEmail/{id}
- **Action:** `App\Http\Controllers\AttendanceController@generateEmail`
- **Name:** `generateEmail`
- **Path Parameters:** {id}
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

## Client-log

### `GET` /client-log/{id}/download
- **Action:** `App\Http\Controllers\ClientController@exportClientLog`
- **Name:** `exportClientLog`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `GET` /client-log/return/{id}/download
- **Action:** `App\Http\Controllers\ClientController@exportClientLogWithReturn`
- **Name:** `exportClientLogWithReturn`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

## Client

### `GET` /client/{id}/product/log/download
- **Action:** `App\Http\Controllers\ClientController@exportClientProductLog`
- **Name:** `exportClientProductLog`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

## Pdf

### `GET` /pdf/sale/{id}
- **Action:** `App\Http\Controllers\PDFController@exportSale`
- **Name:** `pdf.sale`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `GET` /pdf/certify-invoice/{id}
- **Action:** `App\Http\Controllers\PDFController@exportCertifyInvoice`
- **Name:** `pdf.certify-invoice`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `GET` /pdf/multi-sales
- **Action:** `App\Http\Controllers\PDFController@exportMultiSales`
- **Name:** `pdf.multi-sales`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /pdf/multi-certify-invoices
- **Action:** `App\Http\Controllers\PDFController@exportMultiCertifyInvoices`
- **Name:** `pdf.multi-certify-invoices`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Dashboard

### `GET` /dashboard/vacations
- **Action:** `App\Http\Controllers\DashboardController@getEmployeeInVacation`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /dashboard/incoming-vacations
- **Action:** `App\Http\Controllers\DashboardController@getIncomingVacations`
- **Name:** `incoming-vacations`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /dashboard/maintenances/incoming
- **Action:** `App\Http\Controllers\DashboardController@getIncoming`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /dashboard/maintenances/recent
- **Action:** `App\Http\Controllers\DashboardController@getRecent`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /dashboard/maintenances
- **Action:** `App\Http\Controllers\DashboardController@getMaintenance`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /dashboard/admin
- **Action:** `App\Http\Controllers\DashboardController@getAdminDashboard`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Auth

### `POST` /auth/login
- **Action:** `App\Http\Controllers\AuthController@Login`
- **Path Parameters:** None
- **Body Parameters:**
  - `email`
  - `password`
- **Returns:** JSON Object / Array

## Roles

### `GET` /roles/list
- **Action:** `App\Http\Controllers\RoleController@list`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /roles/store
- **Action:** `App\Http\Controllers\RoleController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `role`
- **Returns:** JSON Object / Array

### `POST` /roles/update/{id}
- **Action:** `App\Http\Controllers\RoleController@update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `role`
- **Returns:** JSON Object / Array

### `DELETE` /roles/delete/{id}
- **Action:** `App\Http\Controllers\RoleController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Permissions

### `GET` /permissions/list
- **Action:** `App\Http\Controllers\PermissionController@list`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /permissions/store
- **Action:** `App\Http\Controllers\PermissionController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `action`
  - `subject`
- **Returns:** JSON Object / Array

### `POST` /permissions/update/{id}
- **Action:** `App\Http\Controllers\PermissionController@update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `action`
  - `subject`
- **Returns:** JSON Object / Array

### `DELETE` /permissions/delete/{id}
- **Action:** `App\Http\Controllers\PermissionController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Assets

### `GET` /assets/list
- **Action:** `App\Http\Controllers\AssetController@list`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /assets/{id}/components
- **Action:** `App\Http\Controllers\AssetController@getComponents`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /assets/store
- **Action:** `App\Http\Controllers\AssetController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
- **Returns:** JSON Object / Array

### `POST` /assets/update/{id}
- **Action:** `App\Http\Controllers\AssetController@update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
- **Returns:** JSON Object / Array

### `DELETE` /assets/delete/{id}
- **Action:** `App\Http\Controllers\AssetController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Components

### `GET` /components/list
- **Action:** `App\Http\Controllers\ComponentController@list`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /components/store
- **Action:** `App\Http\Controllers\ComponentController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `asset_id`
- **Returns:** JSON Object / Array

### `POST` /components/update/{id}
- **Action:** `App\Http\Controllers\ComponentController@update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
  - `asset_id`
- **Returns:** JSON Object / Array

### `DELETE` /components/delete/{id}
- **Action:** `App\Http\Controllers\ComponentController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Maintenances

### `GET` /maintenances/list
- **Action:** `App\Http\Controllers\MaintenanceController@list`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /maintenances/store
- **Action:** `App\Http\Controllers\MaintenanceController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `component_id`
  - `asset_id`
  - `technician_assigned_id`
  - `maintenance_date`
  - `next_maintenance_date`
  - `notes`
  - `status`
- **Returns:** JSON Object / Array

### `POST` /maintenances/update/{id}
- **Action:** `App\Http\Controllers\MaintenanceController@update`
- **Path Parameters:** {id}
- **Body Parameters:**
  - `name`
  - `component_id`
  - `technician_assigned_id`
  - `asset_id`
  - `maintenance_date`
  - `next_maintenance_date`
  - `notes`
  - `status`
- **Returns:** JSON Object / Array

### `DELETE` /maintenances/delete/{id}
- **Action:** `App\Http\Controllers\MaintenanceController@delete`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /maintenances/recent
- **Action:** `App\Http\Controllers\MaintenanceController@recent`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Response

## User

### `GET` /user
- **Action:** `Closure`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Response

## Zk-mobile

### `POST` /zk-mobile/login
- **Action:** `App\Http\Controllers\ZKMobileApiController@login`
- **Path Parameters:** None
- **Body Parameters:**
  - `email`
  - `password`
- **Returns:** JSON Object / Array

### `GET` /zk-mobile/employees
- **Action:** `App\Http\Controllers\ZKMobileApiController@getEmployees`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /zk-mobile/attendance
- **Action:** `App\Http\Controllers\ZKMobileApiController@getAttendance`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Zk-assignments

### `GET` /zk-assignments/zk-employees
- **Action:** `App\Http\Controllers\ZKAssignmentController@getZKEmployees`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /zk-assignments/unlinked-employees
- **Action:** `App\Http\Controllers\ZKAssignmentController@getUnlinkedEmployees`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /zk-assignments/available-zk
- **Action:** `App\Http\Controllers\ZKAssignmentController@getAvailableZKEmployees`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /zk-assignments/link
- **Action:** `App\Http\Controllers\ZKAssignmentController@linkEmployee`
- **Path Parameters:** None
- **Body Parameters:**
  - `assigned_at`
  - `employee_id`
  - `zk_employee_id`
- **Returns:** JSON Object / Array

### `POST` /zk-assignments/release
- **Action:** `App\Http\Controllers\ZKAssignmentController@releaseAssignment`
- **Path Parameters:** None
- **Body Parameters:**
  - `released_at`
  - `employee_id`
- **Returns:** JSON Object / Array

### `GET` /zk-assignments/history/{employee_id}
- **Action:** `App\Http\Controllers\ZKAssignmentController@getEmployeeHistory`
- **Path Parameters:** {employee_id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /zk-assignments/attendance-history/{employee_id}
- **Action:** `App\Http\Controllers\ZKAssignmentController@getAttendanceHistory`
- **Path Parameters:** {employee_id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /zk-assignments/attendance-by-date/{date}
- **Action:** `App\Http\Controllers\ZKAssignmentController@getAttendanceByDate`
- **Path Parameters:** {date}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `GET` /zk-assignments/zk-history/{zk_employee_id}
- **Action:** `App\Http\Controllers\ZKAssignmentController@getZKHistory`
- **Path Parameters:** {zk_employee_id}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Companies

### `GET` /companies/list
- **Action:** `App\Http\Controllers\CompanyController@index`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /companies/store
- **Action:** `App\Http\Controllers\CompanyController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `name`
  - `description`
  - `address`
  - `address2`
  - `phone`
  - `email`
  - `NRC`
  - `NIF`
  - `NART`
  - `NIS`
  - `capitale`
- **Returns:** JSON Object / Array

### `GET` /companies/show/{company}
- **Action:** `App\Http\Controllers\CompanyController@show`
- **Path Parameters:** {company}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /companies/update/{company}
- **Action:** `App\Http\Controllers\CompanyController@update`
- **Path Parameters:** {company}
- **Body Parameters:**
  - `name`
  - `description`
  - `address`
  - `address2`
  - `phone`
  - `email`
  - `NRC`
  - `NIF`
  - `NART`
  - `NIS`
  - `capitale`
- **Returns:** JSON Object / Array

### `DELETE` /companies/delete/{company}
- **Action:** `App\Http\Controllers\CompanyController@destroy`
- **Path Parameters:** {company}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

## Real-logistics-invoices

### `GET` /real-logistics-invoices/list
- **Action:** `App\Http\Controllers\RealLogisticsInvoiceController@index`
- **Path Parameters:** None
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /real-logistics-invoices/store
- **Action:** `App\Http\Controllers\RealLogisticsInvoiceController@store`
- **Path Parameters:** None
- **Body Parameters:**
  - `invoice_date`
  - `client_id`
  - `notes`
  - `items`
  - `items.*.product_id`
  - `items.*.product_name`
  - `items.*.quantity`
  - `items.*.price`
- **Returns:** JSON Object / Array

### `GET` /real-logistics-invoices/show/{realLogisticsInvoice}
- **Action:** `App\Http\Controllers\RealLogisticsInvoiceController@show`
- **Path Parameters:** {realLogisticsInvoice}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /real-logistics-invoices/update/{realLogisticsInvoice}
- **Action:** `App\Http\Controllers\RealLogisticsInvoiceController@update`
- **Path Parameters:** {realLogisticsInvoice}
- **Body Parameters:**
  - `invoice_date`
  - `client_id`
  - `notes`
  - `status`
  - `items`
  - `items.*.product_id`
  - `items.*.product_name`
  - `items.*.quantity`
  - `items.*.price`
- **Returns:** JSON Object / Array

### `DELETE` /real-logistics-invoices/delete/{realLogisticsInvoice}
- **Action:** `App\Http\Controllers\RealLogisticsInvoiceController@destroy`
- **Path Parameters:** {realLogisticsInvoice}
- **Body Parameters:** None
- **Returns:** JSON Object / Array

### `POST` /real-logistics-invoices/preview-pdf
- **Action:** `App\Http\Controllers\RealLogisticsInvoiceController@previewPdf`
- **Path Parameters:** None
- **Body Parameters:**  Variable / Undefined (Checks controller logic)
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

### `GET` /real-logistics-invoices/export-pdf/{id}
- **Action:** `App\Http\Controllers\RealLogisticsInvoiceController@exportPdf`
- **Path Parameters:** {id}
- **Body Parameters:** None
- **Returns:** Data Payload (JSON implied by Laravel API wrapper)

