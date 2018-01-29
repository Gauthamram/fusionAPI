# fusionAPI
Fusion API for internal and external stakeholders
## Deployment as new instance:

- Git clone the repository from git hub link provided
- Go into the folder and rename  file named .env.example to .env change the values 
- Add 
	WEB_URL="The portal that is going to use the api"
	API_LOGIN_LINK="The portal login url"
- Make diretcory bootstrap/cache directory
- Make directories storage/framework/cache, storage/framework/sessions, storage/framework/views and make sure they are writable
- Run composer install/update
- Run php artisan key:generate
- Run  php artisan cache:clear
- Run  php artisan view:clear

## 
