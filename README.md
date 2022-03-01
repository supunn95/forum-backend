
## Overview

This is backend API for front-end Vus.js Forum application. Using this forum users able to ask questions related to the product. Users can answer to the questions as well.

## set up instructions

-   create ‘.env’ file and copy & paste all data inside config_files/env data.txt to .env file
-   run ‘composer update’ command
-   run ‘composer dump-autoload’ command

if you like to use fresh database

-   create new database in local machine and set DB data in .env file
-   run ‘php artisan migrate’ command
-   run 'php artisan db:seed' command
-   run 'php artisan passport:install' command

otherwise import DB file in config_files folder and use below credentials to login 

-   admin user
    -   email - admin@gmail.com
    -   password - 1234

-   user
    -   email - test.user@gmail.com
    -   password - 1234
 

