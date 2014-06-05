# API Input Validator

[![Build Status](https://travis-ci.org/renegare/aiv.png?branch=master)](https://travis-ci.org/renegare/aiv)
[![Coverage Status](https://coveralls.io/repos/renegare/aiv/badge.png)](https://coveralls.io/r/renegare/aiv)

## Requirements

* PHP 5.4
* composer (preferably latest)

## Installation

```
$ composer require renegare/aiv:dev-master
```

## Usage Examples:

### Silex Usage
```
<?php

$app = new \Silex\Application();
$app->register(new \AIV\Integration\SilexProvider, [
    'aiv.validators' => [
        'test-name' => [
            'options' => [
                'allow.extra.params' => true,
                'allow.missing.params' => true
            ],
            'params' => [
                'name' => [
                    'not.blank',
                    [
                        'type' => 'length',
                        'options' => ['min' => 2, 'max' => 20]]],
                'email' => ['not.blank', '%email.validator%'],
                'password' => ['not.blank']]]]]);

$app['email.validator'] = $app->share(function() {
    return new \Symfony\Component\Validator\Constraints\Email;
});

$app->post('/', function(Application $app) {
    $apiValidator = $app['aiv'];
    if($apiValidator->hasErrors('test-name')) {
        return (string) $apiValidator->getErrors('test-name');
    } else {
        return sprintf('You sent me valid data:<br /><pre>%s</pre>',
            print_r($apiValidator->getData('test-name'), true));
    }
});

$app->run();

```

### More

TBC

## Test

Check out the repo and from the top level directory run the
following command (xdebug required for coverage):
˜
```
$ composer update && vendor/bin/phpunit --coverage-text
```

## Behind The Scene Lib

The actual validation of data is handled by: Symfony/Validator Component
