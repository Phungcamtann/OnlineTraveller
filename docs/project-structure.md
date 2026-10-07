# Cấu trúc thư mục OnlineTraveller

Cây dưới đây mô tả bộ khung được commit. `.gitkeep` giữ các thư mục chưa có nội dung trong Git. `config/local.php` chỉ tạo trên từng máy từ `local.example.php`, không nằm trong cây được commit.

```text
OnlineTraveller/
├── .gitignore
├── README.md
├── app/
│   ├── controllers/
│   │   ├── AccommodationController.php
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── BookingController.php
│   │   ├── CategoryController.php
│   │   ├── DiscussionController.php
│   │   ├── DocumentController.php
│   │   ├── HomeController.php
│   │   ├── HostController.php
│   │   ├── PaymentController.php
│   │   ├── PostController.php
│   │   ├── PromotionController.php
│   │   ├── ReviewController.php
│   │   ├── ScheduleController.php
│   │   └── ServiceController.php
│   ├── helpers/
│   │   ├── money.php
│   │   ├── upload.php
│   │   └── view.php
│   ├── integrations/
│   │   ├── MomoClient.php
│   │   ├── OpenWeatherClient.php
│   │   └── RssClient.php
│   ├── middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   └── RoleMiddleware.php
│   ├── models/
│   │   ├── Accommodation.php
│   │   ├── Booking.php
│   │   ├── Category.php
│   │   ├── DiscussionMessage.php
│   │   ├── Payment.php
│   │   ├── Post.php
│   │   ├── Promotion.php
│   │   ├── Receipt.php
│   │   ├── Review.php
│   │   ├── Service.php
│   │   ├── ServiceImage.php
│   │   ├── ServiceSchedule.php
│   │   └── User.php
│   ├── services/
│   │   ├── BookingService.php
│   │   ├── DocumentService.php
│   │   ├── PaymentService.php
│   │   └── PricingService.php
│   └── views/
│       ├── admin/
│       │   ├── .gitkeep
│       │   ├── bookings/
│       │   │   └── .gitkeep
│       │   ├── categories/
│       │   │   └── .gitkeep
│       │   ├── dashboard.php
│       │   ├── discussions/
│       │   │   └── .gitkeep
│       │   ├── payments/
│       │   │   └── .gitkeep
│       │   ├── posts/
│       │   │   └── .gitkeep
│       │   ├── promotions/
│       │   │   └── .gitkeep
│       │   ├── services/
│       │   │   └── .gitkeep
│       │   └── users/
│       │       └── .gitkeep
│       ├── auth/
│       │   └── .gitkeep
│       ├── bookings/
│       │   └── .gitkeep
│       ├── documents/
│       │   └── .gitkeep
│       ├── errors/
│       │   ├── 403.php
│       │   ├── 404.php
│       │   └── 500.php
│       ├── home/
│       │   └── .gitkeep
│       ├── host/
│       │   ├── .gitkeep
│       │   ├── accommodations/
│       │   │   └── .gitkeep
│       │   ├── bookings/
│       │   │   └── .gitkeep
│       │   ├── dashboard.php
│       │   ├── schedules/
│       │   │   └── .gitkeep
│       │   └── services/
│       │       └── .gitkeep
│       ├── layouts/
│       │   └── .gitkeep
│       ├── partials/
│       │   └── .gitkeep
│       ├── payments/
│       │   └── .gitkeep
│       ├── posts/
│       │   └── .gitkeep
│       ├── profile/
│       │   └── .gitkeep
│       └── services/
│           ├── .gitkeep
│           ├── index.php
│           ├── partials/
│           │   ├── accommodation.php
│           │   ├── discussion.php
│           │   ├── reviews.php
│           │   ├── schedule-picker.php
│           │   └── weather-map.php
│           └── show.php
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── integrations.php
│   ├── local.example.php
│   └── payment.php
├── core/
│   ├── Autoloader.php
│   ├── BaseController.php
│   ├── BaseModel.php
│   ├── Database.php
│   ├── Request.php
│   ├── Response.php
│   └── Router.php
├── database/
│   ├── changes/
│   │   └── .gitkeep
│   ├── install.sql
│   ├── procedures/
│   │   ├── 01_users_categories.sql
│   │   ├── 02_services_bookings.sql
│   │   ├── 03_promotions_payments.sql
│   │   └── 04_community_content.sql
│   ├── schema.sql
│   └── seed_data.sql
├── docs/
│   ├── .gitkeep
│   ├── analysis-design/
│   │   └── .gitkeep
│   ├── architecture.md
│   ├── database/
│   │   └── procedure-contracts.md
│   ├── diagrams/
│   │   └── .gitkeep
│   ├── payment-sandbox.md
│   ├── project-structure.md
│   ├── setup.md
│   └── team-assignment.md
├── public/
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/
│   │   │   ├── .gitkeep
│   │   │   ├── admin.css
│   │   │   ├── app.css
│   │   │   └── host.css
│   │   ├── images/
│   │   │   └── .gitkeep
│   │   ├── js/
│   │   │   ├── .gitkeep
│   │   │   ├── app.js
│   │   │   ├── booking.js
│   │   │   ├── discussion.js
│   │   │   ├── payment.js
│   │   │   ├── review.js
│   │   │   ├── service-search.js
│   │   │   └── weather-map.js
│   │   └── vendor/
│   │       └── .gitkeep
│   ├── index.php
│   └── uploads/
│       ├── .htaccess
│       ├── avatars/
│       │   └── .gitkeep
│       ├── posts/
│       │   └── .gitkeep
│       └── services/
│           └── .gitkeep
├── routes/
│   ├── api.php
│   ├── callbacks.php
│   └── web.php
├── scripts/
│   ├── expire_bookings.php
│   └── reconcile_payments.php
└── storage/
    ├── cache/
    │   └── .gitkeep
    ├── documents/
    │   └── .gitkeep
    ├── logs/
    │   └── .gitkeep
    └── tmp/
        └── .gitkeep
```

PHP/SQL và assets hiện còn TODO. Các quy ước nghiệp vụ nằm trong [architecture.md](architecture.md); hướng dẫn môi trường nằm trong [setup.md](setup.md). Không tạo thêm thư mục online-traveler lồng vào repository hiện tại.
