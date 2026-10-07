-- MySQL CLI manifest; run from the project root on an empty local database.
-- Schema/procedure/seed files are currently scaffolds, not a working database.
SOURCE database/schema.sql;
SOURCE database/procedures/01_users_categories.sql;
SOURCE database/procedures/02_services_bookings.sql;
SOURCE database/procedures/03_promotions_payments.sql;
SOURCE database/procedures/04_community_content.sql;
SOURCE database/seed_data.sql;
