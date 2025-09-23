<p align="center">
  <img src="https://raw.githubusercontent.com/jdsantos/laradocker/main/docs/logo.png" height="150" alt="Laradocker logo">
  <p align="center">
    <a href="https://github.com/jdsantos/laradocker/actions"><img src="https://img.shields.io/github/actions/workflow/status/jdsantos/laradocker/tests.yml?label=tests&style=round-square" alt="Build Status"></a>
    <a href="https://packagist.org/packages/jdsantos/laradocker"><img src="https://poser.pugx.org/jdsantos/laradocker/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/jdsantos/laradocker"><img alt="Latest Version" src="https://img.shields.io/packagist/v/jdsantos/laradocker"></a>
    <a href="https://packagist.org/packages/jdsantos/laradocker"><img src="https://poser.pugx.org/jdsantos/laradocker/license.svg" alt="License"></a>
  </p>
</p>

---

`jdsantos/laradocker` A Laravel package to seamlessly integrate Docker into your application. It provides an easy way to set up Docker for your Laravel project with minimal configuration.

### Prerequisites

Ensure you have PHP (8.3+), Composer and Docker installed on your machine before proceeding. This package requires also that you run it inside a Laravel 12+ project:

- [Laravel 12+](https://getcomposer.org/download)
- [Docker](https://docs.docker.com/get-docker/)
- [PHP 8.3+](https://php.net/downloads)
- [Composer](https://getcomposer.org/download)

### 🚀 Installation & Usage

Inside your Laravel project folder, simply run the following commands:

1. **Require the Package**: Install the `jdsantos/laradocker` package as a development dependency:

   `composer require --dev jdsantos/laradocker`

2. **Install Laradocker**: Launch the installer and follow the steps to include all necessary files inside your project folder:

   `php artisan laradocker:install`

3. **Try it out!**: Now you can run your Laravel app using Docker like this:

   `docker run -p 80:80 -v laravel_storage:/opt/laravel/storage --rm -it $(docker build -q .)`

It works also if you're using [Podman](https://podman.io)

   `podman run -p 80:80 -v laravel_storage:/opt/laravel/storage --rm -it $(podman build -q .)`


### 🛢 Databases support

Laradocker currently supports the following databases:

 Database  | Version |  Status
:---------|:----------:|:----------:
SQLite            | 3.26.0+   |  ✅        
Mysql             | 5.7+      |  ✅     
MariaDB           | 10.3+     |  ✅    
PostgreSQL        | 10.0+     |  ✅  
SQLServer         | 2017+     |  ✅


### Contributing

Any contributions to this project are more than welcome. Feel free to reach us and we will gladly include any improvements or ideas that you may have.
Please, fork this repository, make any changes and submit a Pull Request and we will get in touch!

### Contributors

| <a href="https://jdsantos.github.io" target="_blank">**Jorge Santos**</a> | <a href="https://walissonaguirra.github.io" target="_blank">**Walisson Aguirra**</a> |
|:---:|:---:|
| [![jdsantos](https://avatars1.githubusercontent.com/u/1708961?v=3&s=50)](http://jdsantos.github.io) | [![walissonaguirra](https://avatars.githubusercontent.com/u/53498071?v=4&s=50)](https://github.com/walissonaguirra) |
| <a href="https://github.com/jdsantos" target="_blank">`github.com/jdsantos`</a> | <a href="https://github.com/walissonaguirra" target="_blank">`github.com/walissonaguirra`</a> |

### Support

The easiest way to seek support is by submiting an issue on this repo.

---

