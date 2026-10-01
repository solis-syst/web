# Solis Web Backend

PHP/MySQL backend for Solis telemetry and account management.

## Structure

- /api/telemetry.php receives anonymous product telemetry.
- /api/status.php returns server-controlled user status and premium entitlement.
- /admin/ provides the management dashboard.
- /database.sql creates the MySQL schema.
- /config/config.php contains database configuration.

## Setup

1. Create a MySQL database and run database.sql.
2. Set the database credentials in config/config.php, or move them to environment-backed constants before deployment.
3. Generate an admin password hash with PHP's password_hash().
4. Insert the first admin into admin_users.
5. Deploy the directory under a PHP-enabled HTTPS host.
6. Point the Solis client telemetry and entitlement requests at the API.

Do not commit real database credentials or production secrets to GitHub.

Telemetry is intentionally limited to product data such as app version, platform, country supplied by the client, events, and coarse metadata. The API hashes the source IP instead of storing it directly.
