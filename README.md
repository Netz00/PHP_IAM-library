# PHP User Managment

Simple storage detached identity and access managment written in vanilla PHP.

For anyone looking forward to learn or simple implementaton.

Currently only session based authentication is supported, but it can be easily be updated to support token based authentication.

## Features:

- user register
- user login
- session managment
- remember me cookies
- password reset

## Usage

1. Copy ./class content to your project
2. Provice implementation of [UserStorage](./class/class.UserStorage.inc.php) interface
   - database, text file or any type of storage you can access can be used
3. Instantiate ...

Example of implementation with MySQL can be found inside ./example directory, [here](./examples/mysql/index.php)

## Documentation

- DI design pattern is used with singleton classes.
