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
class SilexProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app['aiv'] = $app->share(function(Application $app) {
            $manager = new Manager();

            $validators = $app['aiv.validators'];
            foreach($validators as $name => $validatorConstraints) {
                $validator = new Validator();
                $validator->setConstraints($validatorConstraints);
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
}
