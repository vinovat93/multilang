Docker setup:
IMPORTANT : Make sure that you have already installed Docker Desktop: ``https://www.docker.com/products/docker-desktop/``

Step 1 : Run ``cd backend``
Step 2 : Create `.env` file and copy the content from `.env.example`
Step 3 : Run in terminal ``docker compose build``
Step 4 : Run in terminal ``docker compose up -d``
Step 5 : Run in terminal `` docker exec -it laravel_app_container composer install``\
Step 6 : Run in terminal `` docker exec -it laravel_app_container php artisan migrate``\

Tests:
    To run the tests you can use this command : ``docker exec -it laravel_app_container ./vendor/bin/phpunit``

Endpoints:

* Endpoint add new language  => [POST method] http://localhost:8200/api/language
* Endpoint add new original text => [POST method] http://localhost:8200/api/original-text
* Endpoint add translation for a original text  => [POST method] http://localhost:8200/api/translator
* Endpoint update translation for a original text  => [PUT method] http://localhost:8200/api/translator/{id}
* Endpoint retrive translations for a specific language  => [GET method] http://localhost:8200/api/translator/{id}
* Endpoint delete translation for a original text  => [DELETE method]   http://localhost:8200/api/translator/{idd}

