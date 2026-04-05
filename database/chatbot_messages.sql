CREATE TABLE IF NOT EXISTS chatbot_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL DEFAULT '',
    message TEXT NOT NULL,
    sender ENUM('user', 'ai') NOT NULL,
    message_type VARCHAR(32) NOT NULL DEFAULT 'general',
    intent VARCHAR(64) NOT NULL DEFAULT 'GENERAL',
    category VARCHAR(128) NOT NULL DEFAULT '',
    ai_used TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session (session_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE packages ADD COLUMN category VARCHAR(128) NULL DEFAULT NULL AFTER description;
