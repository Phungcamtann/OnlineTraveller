-- ==============================================================================
-- ONLINETRAVELLER - TOÀN BỘ QUY TRÌNH CÀI ĐẶT CƠ SỞ DỮ LIỆU
-- Sử dụng: Trong MySQL CLI (hoặc import qua phpMyAdmin)
-- Cách chạy từ Terminal:
--   mysql -u root -p online_traveller < database/schema.sql
--   mysql -u root -p online_traveller < database/procedures/01_users_categories.sql
--   mysql -u root -p online_traveller < database/procedures/02_services_bookings.sql
--   mysql -u root -p online_traveller < database/procedures/03_promotions_payments.sql
--   mysql -u root -p online_traveller < database/procedures/04_community_content.sql
--   mysql -u root -p online_traveller < database/seed_data.sql
-- Hoặc trong MySQL client console:
--   SOURCE database/install.sql;
-- ==============================================================================

SOURCE database/schema.sql;
SOURCE database/procedures/01_users_categories.sql;
SOURCE database/procedures/02_services_bookings.sql;
SOURCE database/procedures/03_promotions_payments.sql;
SOURCE database/procedures/04_community_content.sql;
SOURCE database/seed_data.sql;
