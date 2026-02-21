-- OAuth client table for PostgreSQL (matches schema.xml).
-- Create database first: createdb -U oauth oauth
-- Then run: psql -U oauth -d oauth -f oauth_client.sql

CREATE TABLE IF NOT EXISTS oauth_client (
    id SERIAL PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    secret VARCHAR(255),
    name VARCHAR(255) NOT NULL,
    redirect_uri VARCHAR(512) NOT NULL,
    is_confidential BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE UNIQUE INDEX IF NOT EXISTS oauth_client_identifier_uq ON oauth_client (identifier);

-- Seed example client. Generate secret with: php -r "echo password_hash('abc123', PASSWORD_BCRYPT);"
-- Then: INSERT INTO oauth_client (identifier, secret, name, redirect_uri, is_confidential)
--       VALUES ('THE_CLIENT_ID', '$2y$10$9.CRHLyk4a8mkVh/9HMX.uIttkhGpI7DZV12Br.XWGJDDa77gde/2', 'THE_CLIENT_ID', 'http://localhost:8081/callback', TRUE);
