# Custom OAuth server based on PHP

## PostgreSQL and Propel

Client data is stored in PostgreSQL and accessed via the [Propel](https://propelorm.org/) ORM.

1. **Database**: Create a PostgreSQL database and the `oauth_client` table:
   - Create DB: `createdb -U postgres oauth` (or use user `oauth` and update `propel.php`).
   - Run the DDL: `psql -U oauth -d oauth -f generated-sql/oauth_client.sql`

2. **Propel config**: Edit `propel.php` if needed (host, port, dbname, user, password). Then run:
   - `vendor/bin/propel config:convert`
   so that `generated-conf/config.php` is up to date.

3. **Optional – generate Propel model classes**: To use full ORM models (e.g. `OauthClientQuery`), install the PHP `dom` extension and run:
   - `vendor/bin/propel model:build`
   - `composer dump-autoload`
   The current `ClientRepository` uses Propel’s connection and raw SQL so the app works without this step.

## Running inline

1. Install dependencies: `composer install`

2. Start the application: `php -S localhost:8000 -t src/public`

## Running on NginX

1. Copy the application to `/var/www/oauth`

2. Copy `.env.example` to `/var/www/oauth/.env` and update the relevant values.

3. Use the example NginX file `deployment/oauth` for server config to be saved on `/etc/nginx/sites-available/oauth`