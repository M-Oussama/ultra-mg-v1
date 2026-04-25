💰 CASHBOOK MODULE (LARAVEL INTEGRATION PLAN)
🧠 MODULE OVERVIEW

We are adding a Cashbook financial tracking module to an existing system that already has:

users
authentication
business logic structure (unknown but existing)

This module introduces:

Cashbooks (ledgers)
Transactions (income/expense)
Optional organization grouping (if not already present or incomplete)

Each cashbook is linked to a user scope (and optionally organization if needed).

🧩 IMPORTANT DECISION (VERY IMPORTANT)

Since your system already exists:

👉 We assume:

users already exist
auth already exists
system already has structure

So we only enforce:

Option A (recommended)

If you already have organization_id → reuse it

Option B (if unclear structure)

Create minimal:

organizations

And link:

users → organization_id
cashbooks → organization_id
🧩 EPIC 1 — CASHBOOK CORE STRUCTURE
🟩 CB-001 — Create Cashbook database structure
🧠 Description

Add Cashbook entity to the existing database without affecting current system.

📦 Table: cashbooks

Fields:

id
name
user_id (creator)
organization_id (optional but recommended)
description (nullable)
timestamps
soft deletes (optional)
🔄 Rules
A cashbook belongs to:
user (creator)
optionally organization
✅ Acceptance Criteria
Cashbook table created
Linked cleanly to existing users
No impact on existing tables
🟩 CB-002 — Create transactions table
🧠 Description

Stores all financial movements inside cashbooks.

📦 Table: transactions

Fields:

id
cashbook_id
user_id (creator)
organization_id (optional)
type (income | expense)
amount
note
transaction_date
timestamps
soft deletes (optional)
🔄 Rules
Every transaction belongs to one cashbook
Must always be scoped to same user/org
✅ Acceptance Criteria
Transactions linked to cashbook correctly
Data is traceable per user
🟩 CB-003 — Optional organization linking check
🧠 Description

Ensure consistency between users and cashbooks.

🔄 Behavior

If organization exists:

enforce same organization_id across:
users
cashbooks
transactions

If NOT:

ignore organization and rely on user_id only
✅ Acceptance Criteria
No cross-user or cross-org leakage
🧩 EPIC 2 — CASHBOOK API (CORE)
🟩 CB-API-001 — Create cashbook API
📡 Endpoint

POST /api/cashbooks

🧠 Description

Creates a new cashbook for the authenticated user.

📥 Payload
name (required)
description (optional)
🔄 Logic
attach user_id = auth user
attach organization_id if exists
create record
📤 Response
created cashbook
success message
✅ Acceptance Criteria
cashbook created correctly
scoped to user/org
🟩 CB-API-002 — Get all cashbooks
📡 Endpoint

GET /api/cashbooks

🧠 Description

Returns all cashbooks for the current user (and org if exists).

🔄 Logic

Filter:

user_id OR organization_id
📤 Response includes:
cashbook id
name
computed balance
last transaction date
⚡ Optimization
use aggregate queries for balance
✅ Acceptance Criteria
correct scoped results
fast response
🟩 CB-API-003 — Get single cashbook
📡 Endpoint

GET /api/cashbooks/{id}

🧠 Description

Returns full cashbook details + financial summary.

📤 Response:
cashbook info
total income
total expense
balance
recent transactions preview
🔐 Security
must belong to user/org
✅ Acceptance Criteria
correct authorization enforced
🟩 CB-API-004 — Delete cashbook
📡 Endpoint

DELETE /api/cashbooks/{id}

🧠 Description

Deletes a cashbook and optionally its transactions.

🔄 Behavior
soft delete recommended
cascade delete transactions optional
⚠️ Rule
only owner can delete
✅ Acceptance Criteria
no orphan data
🧩 EPIC 3 — TRANSACTIONS API
🟩 CB-API-005 — Add transaction
📡 Endpoint

POST /api/cashbooks/{id}/transactions

🧠 Description

Adds income or expense to a cashbook.

📥 Payload:
type (income | expense)
amount
note
transaction_date
🔄 Logic
validate ownership
attach user_id
attach organization_id if exists
📤 Response:
created transaction
✅ Acceptance Criteria
transaction linked correctly
appears in cashbook instantly
🟩 CB-API-006 — Get transactions
📡 Endpoint

GET /api/cashbooks/{id}/transactions

🧠 Description

Fetch all transactions for a cashbook.

🔄 Behavior
ordered by date DESC
filtered by cashbook_id
📤 Response:
full transaction list
✅ Acceptance Criteria
correct ordering
correct scope
🟩 CB-API-007 — Update transaction
📡 Endpoint

PUT /api/transactions/{id}

🧠 Description

Edit existing transaction.

🔄 Behavior
update fields
recalc balances dynamically
⚠️ Rule
must belong to same user/org
✅ Acceptance Criteria
updates reflect instantly
🟩 CB-API-008 — Delete transaction
📡 Endpoint

DELETE /api/transactions/{id}

🧠 Description

Remove a transaction safely.

🔄 Behavior
soft delete preferred
balance updates automatically
✅ Acceptance Criteria
removed from UI + calculations
🧩 EPIC 4 — FINANCIAL ENGINE
🟩 CB-API-009 — Balance computation service
🧠 Description

Central service to calculate cashbook balance.

🔄 Logic

For each cashbook:

income = SUM(type = income)
expense = SUM(type = expense)
balance = income - expense
⚡ Used in:
list API
detail API
reports
✅ Acceptance Criteria
always correct financial values
🟩 CB-API-010 — Cashbook summary API
📡 Endpoint

GET /api/cashbooks/{id}/summary

🧠 Description

Returns financial overview for dashboard.

📤 Response:
total income
total expense
balance
transaction count
last activity date
✅ Acceptance Criteria
fast aggregated response
🧩 EPIC 5 — SECURITY & SCOPE CONTROL
🟩 CB-API-011 — Access control middleware
🧠 Description

Ensure users only access their own cashbooks/transactions.

🔄 Rules
user_id match OR organization_id match
reject unauthorized access
❌ Protection against:
cross-user data access
cross-organization leakage
✅ Acceptance Criteria
no unauthorized access possible
🟩 CB-API-012 — Consistent scoping layer
🧠 Description

Enforce consistent filtering across all queries.

🔄 Behavior
every query auto scoped by:
user_id
organization_id (if exists)
✅ Acceptance Criteria
no manual filtering mistakes
🚀 FINAL SYSTEM FLOW
User logs in
System resolves user + organization
User accesses cashbooks
Cashbooks filtered automatically
User adds transactions
Backend stores + validates
Balance recalculates dynamically
Flutter reflects instantly