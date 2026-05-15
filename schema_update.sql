CREATE TABLE IF NOT EXISTS leave_quotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    sick_limit INT DEFAULT 30,
    personal_limit INT DEFAULT 6,
    year INT NOT NULL,
    UNIQUE KEY (employee_id, year)
);
