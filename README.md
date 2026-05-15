# Smart Attendance System

A modern, web-based attendance and leave management system built with PHP and MySQL, featuring LINE Login integration and a mobile-responsive interface.

## 🚀 Features

### 👤 Employee Features
- **LINE Login:** Quick and secure authentication using LINE OAuth.
- **Attendance Tracking:** Check-in and check-out with automatic status calculation (On-time, Late, Absent).
- **Leave Management:** Submit leave requests (Sick leave, Personal leave) with reason and date range.
- **Personal Dashboard:** View attendance history and leave status.
- **Profile Management:** Update personal information and profile picture.

### 🔑 Admin Features
- **Administrative Dashboard:** Overview of daily attendance statistics and leave requests.
- **User Management:** Manage employee records, roles, and access.
- **Attendance Monitoring:** Detailed reports of all employee attendance.
- **Leave Approval:** Review and approve/reject leave requests from employees.
- **Master Data:** Manage system-wide settings and master records.

## 🛠️ Tech Stack

- **Backend:** PHP 8.0
- **Frontend:** HTML5, CSS3 (Custom Bootstrap 5.3), JavaScript (jQuery)
- **Database:** MySQL 8.0
- **Authentication:** Custom Auth + LINE Login API
- **Containerization:** Docker & Docker Compose

## 📁 Project Structure

```text
smart-attendance-system/
├── api/                # REST-like API endpoints (PHP)
├── frontend/           # UI Components and Role-based pages
│   ├── admin/          # Admin-specific pages
│   ├── employee/       # Employee-specific pages
│   └── layouts/        # Shared layouts (Navbar, Sidenav, etc.)
├── public/             # Static assets (CSS, JS, Images)
├── server/             # Core logic, config, and DB connection
├── docker-compose.yml  # Docker configuration
├── attendance_db.sql   # Database schema and initial data
└── index.php           # Application entry point (Login)
```

## ⚙️ Setup & Installation

### Prerequisites
- Docker and Docker Compose installed on your machine.

### Local Development Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd smart-attendance-system
   ```

2. **Configure Environment Variables:**
   Copy `.env.example` (or use the provided `.env`) and update the values:
   ```bash
   cp .env.example .env
   ```
   *Note: Ensure `LINE_CLIENT_ID` and `LINE_CLIENT_SECRET` are correctly configured for LINE Login functionality.*

3. **Start the containers:**
   ```bash
   docker-compose up -d
   ```

4. **Access the application:**
   The application will be available at `http://localhost:8080`.

5. **Default Credentials:**
   - **Admin:** `admin` / `password` (check `attendance_db.sql` for exact hashes)

## 🗄️ Database Schema

The system uses three primary tables:
- `users`: Stores user credentials, roles, and profile information.
- `attendances`: Records check-in/out times and statuses.
- `leaves`: Manages leave requests and approvals.

## 📝 License

This project is for educational/internal use. See the repository owner for licensing details.
