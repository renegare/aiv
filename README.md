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
        $errors = [];
        foreach($apiValidator->getErrors('test-name') as $violation) {
            $path = preg_replace('/[\[\]]/', '', $violation->getPropertyPath());
            $errors[$path] = $violation->getMessage();
        }
        return sprintf('You have errors: <pre>%s</pre>', print_r($errors, true));
    } else {
        return sprintf('You sent me valid data:<br /><pre>%s</pre>',
            print_r($apiValidator->getData('test-name'), true));
    }
});

$app->run();

```

### JSON Input

Typical JSON Rest API applications take input from the body in the form of json. There
the default input handler will not work as it effectively looks in the $_POST array.

Simply add this code after registering the provider:

```
$app['aiv.input'] = $this->share(function(){
    new \AIV\Input\SymfonyRequest\JSONInput;
});
```

## Test

Check out the repo and from the top level directory run the
following command (xdebug required for coverage):

```
$ composer update && vendor/bin/phpunit --coverage-text
```

## Road Map

- [ ] "Modelesque" Classes that represent validation requirements ($instance_variables vs annotations)

## Behind The Scene Lib

The actual validation of data is handled by: [Symfony/Validator Component][1]

[1]: https://packagist.org/packages/symfony/validator
