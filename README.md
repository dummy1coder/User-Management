Project Purpose.
This project is a user-management system that includes user management features such as:
Create user
Delete user
Change password
Update user details 

Stack used.
Framework: Laravel 
UI Scaffolding: InfyOm AdminLTE Templates
Authentication: Lara

Setup instructions.
1.Create Laravel Project
-composer create-project --prefer-dist laravel/laravel:10.* 
2.Install InfyOm AdminLTE Templates
-Composer require infyomlabs/adminlte-templates
3.Update Composer Dependencies
-Composer update
4.Scaffold UI with AdminLTE and Auth
-php artisan ui adminlte --auth
5.Install and Compile Frontend Assets
-Npm install
-Npm run dev
6.Run Migrations
-Php artisan migrate


API Usage
This project includes a Postman JSON collection that documents and demonstrates how to use the API endpoints. The collection provides details such as:
-API Base URL
-Endpoints for: Creating users
                Updating user profiles
                Deleting users
                Changing passwords
-Required headers, request bodies, and expected responses

You can import the JSON collection into Postman to interact with and test the API easily.
