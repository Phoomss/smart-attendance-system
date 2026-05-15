# Smart Attendance System - AI Instructions

This file contains architectural patterns, coding standards, and workflows specific to the Smart Attendance System.

## 🏗️ Architectural Patterns

- **Separation of Concerns:** The project follows a semi-decoupled architecture.
    - `api/` handles data processing and returns JSON.
    - `frontend/` handles presentation logic using PHP templates.
    - `server/` contains shared utilities and core business logic.
- **Role-Based Access Control (RBAC):** Access is strictly divided between `admin` and `employee` roles. Always verify session roles before performing sensitive operations.
- **Authentication:** Supports both local credentials and LINE OAuth.

## 💻 Coding Standards

- **PHP:** Use PHP 8.0+ features. Prefer `require_once` for dependency management.
- **API Responses:** All API endpoints should return consistent JSON structures:
  ```json
  {
    "status": "success|error",
    "message": "Human readable message",
    "data": {}
  }
  ```
- **Database:** Use PDO for all database interactions to prevent SQL injection.
- **Frontend:** 
    - Use Bootstrap 5.3 for styling.
    - Use SweetAlert2 for user notifications.
    - Prefer AJAX (jQuery) for form submissions to avoid full page reloads.

## 🔄 Workflows

- **Database Updates:** Any schema changes must be documented in a new `.sql` file in the root directory (e.g., `schema_update_v2.sql`) and eventually merged into `attendance_db.sql`.
- **Environment:** Always use `getenv()` in PHP to access configuration defined in `.env` and `docker-compose.yml`.

## 🧪 Testing

- Currently, manual testing is the primary validation method. 
- When adding features, verify both successful paths and error conditions (e.g., invalid input, unauthorized access).
