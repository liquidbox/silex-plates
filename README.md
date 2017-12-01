[![GitHub release](https://img.shields.io/github/release/liquidbox/silex-plates.svg)](https://github.com/liquidbox/silex-plates/releases)
[![license](https://img.shields.io/github/license/liquidbox/silex-plates.svg)](LICENSE)
[![Build Status](https://travis-ci.org/liquidbox/silex-plates.svg?branch=master)](https://travis-ci.org/liquidbox/silex-plates)
[![Code Coverage](https://scrutinizer-ci.com/g/liquidbox/silex-plates/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/liquidbox/silex-plates/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/liquidbox/silex-plates/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/liquidbox/silex-plates/?branch=master)
[![Packagist](https://img.shields.io/packagist/dt/liquidbox/silex-plates.svg)](https://packagist.org/packages/liquidbox/silex-plates)

You are reading the documentation for Silex 2.x. Switch to the documentation for Silex [1.x](../v1.0.0/README.md).

# Plates

The <em>PlatesServiceProvider</em> provides integration with the [Plates](http://platesphp.com) template engine.

## Parameters

* <strong>plates.directory</strong> (optional): The default template directory.
* <strong>plates.file_extension</strong> (optional): The template file extension.
* <strong>plates.folders</strong> (optional): A collection of template folders to add for grouping templates under different namespaces.
* <strong>plates.data</strong> (optional): A collection of data shared with all templates to add.
* <strong>plates.functions</strong> (optional): A collection of template functions to register.
* <strong>plates.extension.asset</strong> (optional): The asset extension directory to load that adds the ability to create "cache busted" asset URLs.

The unlisted parameter <code>plates.path</code> is an alias of <code>plates.directory</code> and only exists to offer drop-in support of package [<code>rych/silex-plates-provider</code>](https://packagist.org/packages/rych/silex-plates-provider). Its use is discouraged and will be removed in the next major update.

## Services

* <strong>plates</strong>: The [<code>Engine</code>](http://platesphp.com/engine) instance. The main way of interacting with Plates.<br />
* <strong>plates.engine_factory</strong>: Factory for <code>Engine</code> instances.
* <strong>plates.loader</strong>: The loader for Plates templates which uses the <code>plates.folders</code>, <code>plates.data</code>, and <code>plates.functions</code> options.
* <strong>plates.extension_loader.asset</strong>: Create new Asset extension instance that adds the ability to create "cache busted" asset URLs.

## Registering

```php
$app->register(new \LiquidBox\Silex\Provider\PlatesServiceProvider(), array(
    'plates.directory' => '/path/to/templates',
    'plates.folders' => array(
        'email' => '/path/to/email/templates',
    ),
));
```

Add Plates as a dependency:

```shell
composer require liquidbox/silex-plates:^2.0
```

## Symfony Components Integration

The <code>PlatesServiceProvider</code> provides additional integration between some Symfony components and Plates. This will provide you with the following additional capabilities.

### Route Support

If you are using the <code>UrlGeneratorServiceProvider</code>, you will have access to the <code>path()</code> and <code>url()</code> functions. You can find more information in the [Symfony Routing documentation](http://symfony.com/doc/2.8/routing.html).

### Security Support

If you are using the <code>SecurityServiceProvider</code>, you will have access to the <code>is_granted()</code> function in templates. You can find more information in the [Symfony Security documentation](http://symfony.com/doc/2.8/security.html).

## Usage

```php
// Add any additional folders
$app['plates']->addFolder('emails', '/path/to/emails');

// Load asset extension
$app['plates.extension_loader.asset']('/path/to/public');

// Create a new template
$template = $app['plates']->make('emails::welcome');
```

## Customization

You can configure the Plates environment before using it when registering the <code>plates</code> service:

```php
$app->register(new \LiquidBox\Silex\Provider\PlatesServiceProvider(), array(
    'plates.data' => array(
        'title' => 'Plates - Native PHP Templates',
    ),
    'plates.functions' => array(
        'uppercase' => function ($string) {
            return strtoupper($string);
        },
    ),
));
```

For more information, check out the [official Plates documentation](http://platesphp.com/).
