# 🌟 Smart Attendance & Leave Management System

A premium, modern web-based attendance and leave management system built with PHP and MySQL. It features LINE Login integration, a responsive Bootstrap 5.3 + SweetAlert2 user interface, real-time AJAX interactions, GPS-based location tracking, attachment uploads for medical/personal leaves, and leave quota tracking.

---

## ✨ Features

### 👤 Employee Dashboard
*   **LINE Login & OAuth:** One-click secure authentication using LINE Login, auto-generating and linking accounts seamlessly.
*   **GPS-Based Clock-In/Out:** Smart check-in/out capturing exact latitude and longitude coordinates via browser geolocation.
*   **Auto-Status Calculation:** Automatically flags check-ins as **On-Time** (before `08:30 AM`) or **Late** (after `08:30 AM`).
*   **Comprehensive Leave Submission:** Submit sick leave (`ลาป่วย`) or personal leave (`ลากิจ`) with:
    *   Specific dates (single/multi-day support).
    *   Detailed text reasons.
    *   **File Attachments:** Upload medical certificates or relevant documents (Images, PDFs).
*   **Interactive History & Cards:** Beautiful responsive grid showing attendance cards and leave statuses.
*   **Profile Management:** View employee codes, edit personal information, and update profile pictures.

### 🔑 Admin Dashboard
*   **Analytics Overview:** Real-time dashboard showing daily attendance metrics, late ratios, and pending leave requests.
*   **Leave Management Hub:** Review and approve/reject leave requests with inline document previews and action logs.
*   **Detailed Attendance Monitoring:** View comprehensive logs of check-ins, check-outs, status indicators, and embedded GPS location maps.
*   **User Management:** Add, edit, remove employees, and manage application roles (`admin` or `employee`).
*   **Leave Quota Master Data:** Configure annual sick leave (default: 30 days) and personal leave (default: 6 days) limit quotas per user via `leave_quotas` table.

---

## 🛠️ Tech Stack & Architecture

*   **Backend Engine:** PHP 8.0+ (utilizing PDO for secure, SQL-injection-proof database transactions).
*   **Frontend UI/UX:** HTML5, Bootstrap 5.3 (customized styling), jQuery (AJAX-driven form handling to avoid full-page reloads), SweetAlert2 for modern alerts, and FontAwesome 6 icons.
*   **Database:** MySQL 8.0 (structured relational schema with foreign key integrity).
*   **Authentication:** Dual support for Local Username/Password + LINE Login API (OpenID Connect / OAuth 2.0).
*   **Containerization:** Docker & Docker Compose (fully decoupled architecture, multi-container network configuration).

### 🏗️ Architectural Patterns
*   **Separation of Concerns:**
    *   `api/`: Decoupled REST-like API endpoints processing request parameters and outputting standard JSON responses.
    *   `frontend/`: Presentation layer using clean PHP templates divided by role dashboards (`admin`, `employee`).
    *   `server/`: Shared business logic layer comprising utility classes, DB connection management, and configurations.
*   **RBAC Security:** Session-based checks validating user identity and roles before executing sensitive administrative or personal transactions.

---

## 📁 Project Structure

```text
smart-attendance-system/
├── api/                        # REST-like JSON API Endpoints (PHP)
│   ├── attendanceApi.php       # Handles clock-in, clock-out, check status
│   ├── attendanceDailyApi.php  # Daily attendance stats for admin dashboard
│   ├── attendanceMonthly.php   # Monthly breakdown for reporting
│   ├── leaveApi.php            # Processes leave creation, updates, and deletion
│   ├── leaveCountApi.php       # Generates data for administrative charts
│   ├── loginApi.php            # Handles credential authentication
│   ├── registerApi.php         # Handles user registration
│   └── userApi.php             # Updates user profile data
├── frontend/                   # UI Templates & Layout Components
│   ├── admin/                  # Administrative views (dashboard, reporting, user management)
│   ├── employee/               # Employee views (dashboard, clock-in modal, leave forms)
│   ├── layouts/                # Shared layout templates (navbar, sidebar, footers)
│   └── components/             # Reusable UI elements (cards, badges)
├── server/                     # Core Business Logic & Infrastructure
│   ├── LineLogin.php           # LINE OAuth 2.0 implementation
│   ├── attendance.php          # Attendance entity models and check-in algorithms
│   ├── auth.php                # Local credential authentication & session control
│   ├── callback.php            # LINE Login redirection handler
│   ├── config.php              # Centralized configuration constants
│   ├── conn.php                # Secure PDO database connector with automatic fallback
│   ├── detailWork.php          # Deep-dive workspace logic
│   ├── exportCsv.php           # Report exporter
│   ├── leave.php               # Leave application entity model
│   └── user.php                # User entity model (registrations, updates)
├── public/                     # Public Static Assets
│   ├── css/                    # Custom CSS layout (main.css)
│   ├── js/                     # AJAX controllers (app.js, attendance.js, auth.js)
│   └── uploads/                # Document and avatar file upload destination
├── docker-compose.yml          # Docker services composition (app & db)
├── Dockerfile                  # PHP 8.0 base container customization
├── attendance_db.sql           # Initial database schema and admin account seed
├── schema_update.sql           # Database migration schema updates
└── index.php                   # Portal entry page (Login / Authentication)
```

---

## ⚙️ Setup & Local Installation

### Prerequisites
*   Docker and Docker Compose installed.

### Installation Steps

1.  **Clone the Repository:**
    ```bash
    git clone <repository-url>
    cd smart-attendance-system
    ```

2.  **Environment Configuration:**
    Copy `.env.example` to create your active `.env` file and customize if needed:
    ```bash
    cp .env.example .env
    ```
    Ensure the configuration parameters align with your environment:
    ```ini
    # Database Configuration
    DB_HOST=db
    DB_NAME=attendance_db
    DB_USER=attendance_user
    DB_PASSWORD=attendance_pass
    DB_ROOT_PASSWORD=rootpassword

    # LINE Login API Credentials
    LINE_CLIENT_ID=your_line_client_id
    LINE_CLIENT_SECRET=your_line_client_secret
    LINE_REDIRECT_URL=http://localhost:8080/server/callback.php

    # System Port
    APP_PORT=8080
    ```

3.  **Boot System Containers:**
    Spin up the PHP application and MySQL database in detached mode:
    ```bash
    docker-compose up -d --build
    ```
    This command automatically initializes the database using `attendance_db.sql` on the first start.

4.  **Database Upgrades & Repairs:**
    If you are setting up this repository on a fresh clone or need to patch missing structure variables, run the database update tools by visiting:
    *   Update Schema: `http://localhost:8080/update_db.php`
    *   Complete Structural Repairs: `http://localhost:8080/complete_db_repair.php`
    
    > [!IMPORTANT]
    > For production environments, delete `update_db.php` and `complete_db_repair.php` once database tables are active and configured.

5.  **Mock Data Generation (Recommended for Evaluation):**
    To populate the system with a rich set of realistic test records (including 5 pre-configured employees, 30 days of randomized weekday attendance history with mock Bangkok GPS coordinates, and various sick/personal leaves with Thai description logs), visit:
    *   Mock Data Seeder: `http://localhost:8080/generate_mock_data.php`
    
    > [!TIP]
    > **Built-in Auto-Healing Engine:** The seeder includes database auto-healing logic. If your database is missing primary key `AUTO_INCREMENT` flags, required GPS columns (`latitude`, `longitude`), leave attachment fields, or the `leave_quotas` table, the seeder automatically repairs your schema before truncating and writing mock data.
    
    > [!IMPORTANT]
    > Running the seeder clears current tables to prevent data conflicts. Delete `generate_mock_data.php` in production environments.

6.  **Access Portal & Default Credentials:**
    Open `http://localhost:8080` in your web browser.
    *   **Admin Access:** `admin` / `password`
    *   **Employee Access:** Log in using any pre-configured employee account (e.g., `somchai`, `somsri`, `john`, `jane`, `wichai` with password `password`) or register a new one.

---

## 🗄️ Database Architecture

The relational model utilizes 4 primary tables to ensure transaction consistency and historical audit logs:

1.  **`users`**:
    *   Stores credentials, identity roles, and dynamic API access tokens.
    *   Supports profile pictures (`picture`) and links LINE profiles to local records via standard emails.
2.  **`attendances`**:
    *   Tracks daily check-in (`attendance_time`) and check-out (`departure_time`) timestamps.
    *   Stores automated computed attendance status (`on_time`, `late`, `absent`).
    *   Tracks exact GPS location metrics (`latitude`, `longitude`) via browser geolocation capture.
3.  **`leaves`**:
    *   Records sick and personal leave durations, reasons, status updates (`pending`, `approved`, `rejected`), and file attachment paths (`attachment_path`).
4.  **`leave_quotas`**:
    *   Maintains employee-specific quotas, limiting yearly sick leave and personal leave days to prevent policy misuse.

---

## 🔒 Security Standards

1.  **SQL Injection Protection:** Every database interaction goes through standard **PDO Parameter Binding** inside pre-compiled statements. Direct raw string interpolation is prohibited.
2.  **Role-Based Security Checks:** The system blocks administrative URLs by checking the session context and ensuring the `role` parameter equals `'admin'`.
3.  **Encrypted Passwords:** Passwords are securely hashed using PHP’s `password_hash()` employing the state-of-the-art **BCRYPT** algorithm.
4.  **Safe OAuth Verification:** LINE authentication uses standard state parameter hashing (`SHA-256`) to protect against CSRF injection attacks during redirection.

---

## 📝 License

This project is for educational/internal use. See the repository owner for licensing details.
