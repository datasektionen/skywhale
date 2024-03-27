# Skywhale
PHP (</3) Laravel application that handles elections. An election is an event with starting and ending date (also containing dates for nomination and acceptance stops). An election has at least one position open for election. 

## API
There is an API for Skywhale. The API is located at ```/api``` (http://val.datasektionen.se/api).

### API endpoints
The following endpoints are based on the above URL.
```
GET /elections          Returns all the current elections as JSON
```

## Required environment variables
```
APP_ENV=production
APP_KEY=12345678901234567890abcdefabcdef
APP_DEBUG=false
APP_LOG_LEVEL=debug
APP_URL=

DB_CONNECTION=
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

LOGIN_API_KEY=
LOGIN_API_URL=https://login.datasektionen.se
LOGIN_FRONTEND_URL=https://login.datasektionen.se
PLS_API_URL=http://pls.datasektionen.se/api
ZFINGER_API_URL=https://zfinger.datasektionen.se
SPAM_API_KEY=
SPAM_API_URL=https://spam.datasektionen.se/api/sendmail
HODIS_API_URL=https://hodis.datasektionen.se
```

## Roadmap
Random features are implemented at a random speed. Post an [issue](https://github.com/datasektionen/skywhale/issues), and maybe it will be implemented. One day.

## Installation and setup
`touch .env; docker compose watch`

If you want to run this without docker, make sure you install composer version 1 (not 2, which is the latest at the moment).

### Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

### Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

### Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

### Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
