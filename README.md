<p align="center">
  <img src="https://raw.githubusercontent.com/jdsantos/laradocker/main/docs/logo.png" height="150" alt="Laradocker logo">
  <p align="center">
    <a href="https://github.com/jdsantos/laradocker/actions"><img src="https://img.shields.io/github/actions/workflow/status/jdsantos/laradocker/tests.yml?label=tests&style=round-square" alt="Build Status"></img></a>
    <a href="https://packagist.org/packages/jdsantos/laradocker"><img src="https://poser.pugx.org/jdsantos/laradocker/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/jdsantos/laradocker"><img alt="Latest Version" src="https://img.shields.io/packagist/v/jdsantos/laradocker"></a>
    <a href="https://packagist.org/packages/jdsantos/laradocker"><img src="https://poser.pugx.org/jdsantos/laradocker/license.svg" alt="License"></a>
  </p>
</p>

---

`jdsantos/laradocker` A Laravel package to seamlessly integrate Docker into your application. It provides an easy way to set up Docker for your Laravel project with minimal configuration.

## Prerequisites

Ensure you have PHP (8.2+), Composer and Docker installed on your machine before proceeding. This package requires also that you run it inside a Laravel 11+ project:

- [Laravel 11+](https://getcomposer.org/download)
- [Docker](https://docs.docker.com/get-docker/)
- [PHP 8.2+](https://php.net/downloads)
- [Composer](https://getcomposer.org/download)

## 🚀 Installation

Inside your Laravel project folder, simply run the following commands:

1. **Require the Package**: Install the `jdsantos/laradocker` package as a development dependency:

   `composer require --dev jdsantos/laradocker`

2. **Install Laradocker**: Installs the necessary files inside your project folder:

   `php artisan laradocker:install`

3. **Try it out!**: Now you can run your Laravel app using Docker like this:

   `docker run -p 80:80 -v laravel_storage:/opt/laravel/storage --rm -it $(docker build -q .)`
