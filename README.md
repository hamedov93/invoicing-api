## Running the project

In order to run this project, you need docker and docker compose plugin installed on your system.

- Clone the repository.
- `cp .env.example .env` and set env variables while keeping mysql config the same.
- Run `docker compose up -d nginx php-fpm mysql`.
- Run `docker compose exec php-fpm bash` to start running artisan commands inside fpm container.
- Run `php artisan migrate`
- Then `php artisan db:seed` This should seed an admin user and required test data into the database.
- Admin user credentails: email: admin@example.com, password: 12345678
- Use the attached postman collection to send API Requests.
- You'll need to issue an access token first for the admin user using `/api/auth/login` endpoint and above credentials, and then use the token in create/fetch invoice endpoints.
- At any point, you can start over by running `php artisan migrate:fresh` and `php artisan db:seed` again.