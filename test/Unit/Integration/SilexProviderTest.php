<?php

namespace AIV\Test\Unit\Integration;

use AIV\Test\BaseTestCase;
use Silex\Application;
use AIV\Integration\SilexProvider;
use Symfony\Component\HttpKernel\Client;

class SilexProviderTest extends BaseTestCase {

    public function testPurpose() {
        $postData = [
            'name' => 'John Smith',
            'email' => 'web@internet.com'];

        $app = new Application();
        $app->register(new SilexProvider, [
            'aiv.validators' => [
                'test-name' => [
                    'name' =>[
                        'not.blank',
                        [
                            'type' => 'length',
                            'options' => ['min' => 2, 'max' => 20]]],
                    'email' => ['not.blank', '%email.validator%']]]]);
        $app['email.validator'] = $app->share(function() {
            return new \Symfony\Component\Validator\Constraints\Email;
        });

        $app->post('/', function(Application $app) use ($postData){
            $this->assertEquals($postData, $app['aiv']->getData('test-name'));
            return '';
        });

        $app['exception_handler']->disable();

        $client = new Client($app, []);
        $client->request('POST', '/', [
            'test-name' => $postData]);
    }


}
