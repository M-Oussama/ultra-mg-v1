# ZK Employee Assignments API

This documentation covers the APIs related to linking physical employees from the `employees` table with ZK device accounts in the `zk_employees` table. This allows reassigning ZK device PINs/IDs to different employees over time while maintaining historical records.

## Base URL
`/api/zk-assignments`

---

## 1. List ZK Employees
Retrieves all employees registered in the ZK device table, along with their current assignment if any.

**URL:** `GET /zk-employees`

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 101,
            "zk_id": 101,
            "name": "ZK John",
            "surname": "Doe",
            "current_assignment": {
                "id": 5,
                "employee_id": 12,
                "zk_employee_id": 101,
                "assigned_at": "2024-01-01 08:00:00",
                "released_at": null,
                "employee": {
                    "id": 12,
                    "name": "Real Employee Name"
                }
            }
        }
    ]
}
```

---

## 2. List Unlinked Employees
Retrieves physical employees who are NOT currently linked to any ZK ID. This includes employees who have never been linked or those whose previous assignments have been ended (released).

**URL:** `GET /unlinked-employees`

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 14,
            "name": "Jane",
            "surname": "Smith",
            "active": true
        }
    ]
}
```

---

## 3. Link Employee to ZK ID
Creates a new assignment between a physical employee and a ZK hardware ID. 
> **Note:** If the employee or the ZK ID already has an active assignment, it will be automatically released (set `released_at = now()`) before creating the new one.

**URL:** `POST /link`

**Body:**
```json
{
    "employee_id": 14,
    "zk_employee_id": 101,
    "assigned_at": "2024-02-23 09:00:00" (optional, defaults to now)
}
```

---

## 4. Release Employee (Unlink)
Ends the current assignment for an employee. Use this when an employee leaves the company or is no longer using the ZK device.

**URL:** `POST /release`

**Body:**
```json
{
    "employee_id": 14,
    "released_at": "2024-02-23 17:00:00" (optional, defaults to now)
}
```

---

## 5. Get Available ZK IDs
Retrieves ZK IDs that are currently NOT assigned to any physical employee.

**URL:** `GET /available-zk`

---

## 6. Get Employee Assignment History
Retrieves the history of all ZK IDs ever assigned to a specific employee.

**URL:** `GET /history/{employee_id}`

---

## 7. Get Employee Attendance History
Retrieves the attendance records for a specific employee across all their ZK assignments. This automatically searches for records using the correct ZK PIN during the specific time periods the employee was assigned that PIN.

**URL:** `GET /attendance-history/{employee_id}`

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1234,
            "employee_id": 101,
            "punched_at": "2024-02-23 08:30:00",
            "type": 0,
            "raw_line": "..."
        }
    ]
}
```

---

## 8. Get Attendance by Date
Retrieves a list of all employees who attended on a specific date, along with their punch details. This automatically resolves which physical employee owned the ZK ID at that specific time.

**URL:** `GET /attendance-by-date/{date}`
> Date format: `YYYY-MM-DD`

**Response:**
```json
{
    "status": "success",
    "date": "2024-02-23",
    "data": [
        {
            "employee_id": 14,
            "name": "Jane",
            "surname": "Smith",
            "total_punches": 2,
            "punches": [
                {
                    "punched_at": "2024-02-23 08:05:00",
                    "type": 0,
                    "zk_id": 101
                },
                {
                    "punched_at": "2024-02-23 17:15:00",
                    "type": 1,
                    "zk_id": 101
                }
            ]
        }
    ]
}
```

---

## 9. Get ZK ID Assignment History
Retrieves the history of all physical employees who have been assigned to a specific ZK ID.

**URL:** `GET /zk-history/{zk_employee_id}`

**Response:**
```json
{
    "status": "success",
    "zk_employee_id": "101",
    "data": [
        {
            "id": 5,
            "employee_id": 12,
            "zk_employee_id": 101,
            "assigned_at": "2024-01-01 08:00:00",
            "released_at": "2024-02-01 17:00:00",
            "employee": {
                "id": 12,
                "name": "John Doe",
                "surname": "Manager"
            }
        }
    ]
}
```
