<?php

namespace AIV\Test\Unit\Integration;

use AIV\Test\BaseTestCase;
use Silex\Application;
use AIV\Integration\SilexProvider;
use Symfony\Component\HttpKernel\Client;

class SilexProviderTest extends BaseTestCase {

    public function testRegister() {
        $app = new Application();
        $app->register(new SilexProvider);

        $app->post('/', function(Application $app){
            $this->assertEquals([
                'name' => 'John Smith'
            ], $app['aiv']->getData('test-name'));
        });

        $app['exception_handler']->disable();
        $app['session.test'] = true;

        $client = new Client($app, []);
        $client->request('POST', '/', [
            'name' => 'John Smith'
        ]);
    }

    public function testBoot() {
        $this->assertTrue(true);
    }
}
