# Repeator

Package for laravel-admin-widgets. This package is help to make dynamic fields for [laravel-admin-widgets](https://github.com/denis-kisel/laravel-admin-widget).

## Installation

Via Composer

``` bash
$ composer require denis-kisel/repeator
```

Add service provider in the config/app.php file
``` php
/*
 * Package Service Providers...
 */
DenisKisel\Repeator\RepeatorServiceProvider::class,
```


Put code in the app/admin/bootstrap.php file
``` php
Encore\Admin\Form::extend('repeat', DenisKisel\Repeator\Repeator::class);
```
## Usage

In the laravel-admin-widgets files
``` php
$form->repeat('items', function (NestedForm $form) {
    $form->text('item', 'Item');
});
```
