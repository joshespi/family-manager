CREATE TABLE IF NOT EXISTS user_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role ENUM('admin','user','parent', 'child') NOT NULL DEFAULT 'user',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);