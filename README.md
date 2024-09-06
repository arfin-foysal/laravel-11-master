## BacBon CRM (Customer Relationship Management)

## Features
- JWT Authentication
- Role Based Authentication
- User Management
- Role Management
- Permission Management
- Docker Support


## Instructions

1. Clone the repository
2. Run `composer install`
3. ENV `cp .env.example .env or copy .env.example .env.`
4. Run `php artisan key:generate`
5. Run `php artisan jwt:secret`
6. Create a database and update the `.env` file
7. if use sqlite Database touch /home/user/path/laravel-11-master/database/database.sqlite
8. Run `php artisan migrate` or `php artisan migrate:fresh --seed`
9. Run `php artisan serve`
10. Open Postman and import the collection from the `postman` directory
11. Swagger API Documentation: `http://localhost:8000/swagger/documentation`



## ========Thank You========
