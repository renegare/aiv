<?php

namespace AIV\Test\Unit\Integration;

use AIV\Test\BaseTestCase;
use Silex\Application;
use AIV\Integration\SilexProvider;
use Symfony\Component\HttpKernel\Client;

class SilexProviderTest extends BaseTestCase {

    public function setup() {
        // Taken from the README.md: START
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
        // Taken from the README.md: END

        $this->app = $app;
    }

    public function provideData() {
        return [
            ['Valid Post Data', [
                'name' => 'John Smith',
                'email' => 'web@internet.com',
                'fav_colour' => 'red'], 'You sent me valid data:'],
            ['Invalid Post Data', [], 'You have errors:'],
            ['Invalid Post Data', ['email' => 'ksdksdk'], 'You have errors:']
        ];
    }

    /**
     * @dataProvider provideData
     */
    public function testValidData($label, $postData, $expectedResponse) {
        $client = new Client($this->app, []);

        $label = 'Test Case: ' . $label;
        $client->request('POST', '/', ['test-name' => $postData]);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk(), $label);
        $this->assertContains($expectedResponse, $response->getContent(), $label);
    }

    /**
     * @dataProvider provideData
     */
    public function testValidJsonData($label, $data, $expectedResponse) {
        $this->app['aiv.input'] = new \AIV\Input\SymfonyRequest\JSONInput;

        $client = new Client($this->app, []);

        $label = 'Test Case: ' . $label;
        $client->request('POST', '/', [], [], [], json_encode(['test-name' => $data]));
        $response = $client->getResponse();
        $this->assertTrue($response->isOk(), $label);
        $this->assertContains($expectedResponse, $response->getContent(), $label);
    }


}
