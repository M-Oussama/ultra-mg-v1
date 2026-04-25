# 📱 SCREENS DOCUMENTATION - CASHBOOK MODULE

This document outlines the screens and UI/UX design philosophy for the Cashbook module.

## 🎨 Design Philosophy (Luminous V1 Style)
The Cashbook module follows the **Luminous V1** design system, prioritizing:
- **Financial Clarity**: High contrast between income and expense values.
- **Premium Feel**: Use of glassmorphism, subtle shadows, and Inter typography.
- **Speed**: Optimized for one-handed operation (FAB, quick swipes).

---

## 1. Cashbook Hub (Main Dashboard)
**Purpose**: Central entry point to view all ledgers and total net balance.

### 🧩 UI Components
- **Global Balance Card**: Large hero card using a premium gradient (`#1E3A8A` to `#7E22CE`).
- **Cashbook Chips**: Filterable chips to switch between different ledgers.
- **Grid View of Ledgers**: Each card shows:
  - Cashbook Name
  - Current Balance (Color-coded)
  - Last activity date
  - Mini sparkline chart of recent 7-day trend.

---

## 2. Cashbook Detail & Transaction List
**Purpose**: View specific ledger history and perform balance analysis.

### 🧩 UI Components
- **Financial HUD**: Sticky header with total Income/Expense breakout.
- **Search & Filter Bar**: Real-time filtering by note or date range.
- **Transaction Cards**:
  - `Income`: Soft green background (`#DCFCE7`), up-arrow icon.
  - `Expense`: Soft red background (`#FEE2E2`), down-arrow icon.
  - Swipe actions: Swipe left to delete, swipe right to edit.
- **Empty State**: Beautifully illustrated empty state if no transactions exist.

---

## 3. Add/Edit Transaction Modal
**Purpose**: Capture financial data with minimal friction.

### 🧩 UI Components
- **Segmented Picker**: High-tactile switch between "Income" and "Expense".
- **Numeric Keyboard**: Custom big-button numeric entry for amounts.
- **Date Picker**: Modern horizontal calendar strip.
- **AI Tagger**: Auto-suggests notes based on past entries.

---

## 4. Reports & Analytics
**Purpose**: Visual summary of monthly/weekly performance.

### 🧩 UI Components
- **Donut Chart**: Categorical breakout of expenses.
- **Comparison Bars**: Monthly performance against previous period.
- **Export FAB**: One-tap PDF/Excel report generation.

---

## 🛠 Architectural Implementation (Flutter)
- **State Management**: BLoC / Provider for real-time balance sync.
- **Animations**: `ImplicitAnimations` for list reordering and balance count-up effect.
- **Offline First**: Local caching with SQLbrite/Hive for instant loading.
