<?php

namespace AIV\Integration;

use AIV\Manager;
use AIV\Validator;
use AIV\Input\SymfonyRequestInput;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * dependant on Silex\SecurityProvider
 * @todo needs to validate options
 */
class SilexProvider implements ServiceProviderInterface, \AIV\ConstraintResolverInterface {

    protected $app;

    public function register(Application $app) {
        $this->app = $app;

        $app['aiv'] = $app->share(function(Application $app) {
            $manager = new Manager();

            $validators = $app['aiv.validators'];
            foreach($validators as $name => $validatorConstraints) {
                $validator = new Validator();
                $validator->setConstraintResolver($this);
                $validator->setConstraints($validatorConstraints['params']);
                $manager->addValidator($name, $validator);
            }

            $manager->setInput($app['aiv.input']);
            return $manager;
        });

        $app['aiv.input'] = $app->share(function(){
            return new SymfonyRequestInput();
        });
    }

    public function boot(Application $app) {
        $app['dispatcher']->addListener(KernelEvents::REQUEST, function(GetResponseEvent $event) use ($app){
            $app['aiv.input']->setRequest($event->getRequest());
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($config) {
        if(is_string($config) && preg_match('/^%(.+)%$/', $config, $matches)) {
            $constraint = $this->app[$matches[1]];
        } else {
            if(is_array($config)) {
                $class = $config['type'];
                $options = $config['options'];
            } else {
                $class = $config;
                $options = null;
            }

            $_class = array_map(function($part){
                return ucfirst($part);
            }, explode('.', $class));
            $_class = 'Symfony\Component\Validator\Constraints\\' . implode('', $_class);
            $class = class_exists($_class)? $_class : $class;

            $constraint = new $class($options);
        }

        return $constraint;
    }
}
