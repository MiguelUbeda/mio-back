# Mio Group Backend - Users management

This project was generated with [Symfony](https://symfony.com/) version 6.2.9.
This application allows to create and manage users. The frontend is built in Angular, while the backend is an API developed with the Symfony framework. JWT authentication is used to ensure user security and authorization. Below are the instructions on how to install and use the application on the backend:

## Requirements

Before you can run and install the application:

- [PHP 8.1](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download/)

## Dependencies
- `@lexik/jwt-authentication-bundle` providing JWT authentication security

## Getting started
- Configure the .env file with the database information.
- Run `composer install` to install composer dependencies to install Composer dependencies
- Run `php bin/console lexik:jwt:generate-keypair` to generate keys for JWT
- Run the database migrations `php bin/console doctrine:migrations:migrate`

## Build

Run `symfony server:start` to serve the project on http://localhost:8000.

## Using the application

### Registration: 
If you don't have an account, click on "Register" within the form. Fill out the new form with your user information and save it. You will be redirected to the authentication page.

### Authentication: 
Enter your user information and you will be redirected to the RSS/ATOM Sources page (From now on you will get data by using JWT token from local storage).

### Logout: 
When you're ready to log out, click on the "Logout" button in the navigation menu. You will be logged out of your account and redirected to the login page.
