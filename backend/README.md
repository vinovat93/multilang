Docker setup:\
Step 1 : run ``docker compose build``\
Step 2 : Create `.env` file and copy the the content from `.env.example`\
Step 3 : Go to app container terminal and run `` docker exec -it laravel-multilang-web-1 composer install && php artisan migrate``\

Endpoints:

* Endpoint add new language  => [POST method] http://localhost:8200/api/language
* Endpoint add new original text => [POST method] http://localhost:8200/api/original-text
* Endpoint add translation for a original text  => [POST method] http://localhost:8200/api/translator
* Endpoint update translation for a original text  => [PUT method] http://localhost:8200/api/translator/2
* Endpoint retrive translations for a specific language  => [GET method] http://localhost:8200/api/translator/2
* Endpoint delete translation for a original text  => [DELETE method]   http://localhost:8200/api/translator/2

