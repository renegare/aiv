<?php

namespace AIV\Test\Unit\Integration;

use AIV\Test\BaseTestCase;
use Silex\Application;
use AIV\Integration\SilexProvider;
use Symfony\Component\HttpKernel\Client;

class SilexProviderTest extends BaseTestCase {

    public function testPurpose() {
        $app = new Application();
        $app->register(new SilexProvider, [
            'aiv.validators' => [
                'test-name' => ['name' =>['not.blank']]]]);

        $app->post('/', function(Application $app){
            $this->assertEquals([
                'name' => 'John Smith'
            ], $app['aiv']->getData('test-name'));
            return '';
        });

        $app['exception_handler']->disable();

        $client = new Client($app, []);
        $client->request('POST', '/', [
            'test-name' => [
                'name' => 'John Smith']]);
    }


}
