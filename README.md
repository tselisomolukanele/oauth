# oauth

Install dependencies: `composer install`

Start the application: `php -S localhost:8000 -t src/public`

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