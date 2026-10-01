CREATE DATABASE IF NOT EXISTS solis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE solis;

CREATE TABLE users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  installation_id VARCHAR(128) NOT NULL,
  email VARCHAR(255) NULL,
  app_version VARCHAR(32) NULL,
  os VARCHAR(64) NULL,
  arch VARCHAR(32) NULL,
  country CHAR(2) NULL,
  status ENUM('active','suspended','banned') NOT NULL DEFAULT 'active',
  premium_plan ENUM('free','monthly','yearly','lifetime') NOT NULL DEFAULT 'free',
  premium_until DATETIME NULL,
  last_seen_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_installation (installation_id),
  KEY idx_users_status (status),
  KEY idx_users_premium (premium_plan, premium_until),
  KEY idx_users_last_seen (last_seen_at)
) ENGINE=InnoDB;

CREATE TABLE telemetry_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  event_name VARCHAR(100) NOT NULL,
  app_version VARCHAR(32) NULL,
  metadata_json JSON NULL,
  ip_hash CHAR(64) NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_events_user_time (user_id, created_at),
  KEY idx_events_name_time (event_name, created_at),
  CONSTRAINT fk_events_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE admin_users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_email (email)
) ENGINE=InnoDB;

CREATE TABLE admin_audit_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  action VARCHAR(64) NOT NULL,
  details TEXT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_audit_user_time (user_id, created_at),
  CONSTRAINT fk_audit_admin FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE RESTRICT,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Generate a password hash with:
-- php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
-- Then insert it:
-- INSERT INTO admin_users (email, password_hash) VALUES ('admin@example.com', 'PASTE_HASH_HERE');
