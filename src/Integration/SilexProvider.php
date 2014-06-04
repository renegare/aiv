<?php

namespace AIV\Integration;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * dependant on Silex\SecurityProvider
 * @todo needs to validate options
 */
class SilexProvider implements ServiceProviderInterface {

    public function register(Application $app) {

    }

    public function boot(Application $app) {}
}
