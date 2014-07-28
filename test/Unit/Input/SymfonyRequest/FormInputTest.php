<?php

namespace AIV\Test\Unit\Input\SymfonyRequest;

use AIV\Test\BaseTestCase;
use AIV\Input\SymfonyRequest\FormInput;
use Symfony\Component\HttpFoundation\Request;

class FormInputTest extends BaseTestCase {

    protected $expectedData = [
        'test-name' => ['...'],
        'something-else' => ['...incorrect...']
    ];

    public function provideRequests() {
        return [
            [Request::create('/', 'GET', $this->expectedData)],
            [Request::create('/', 'POST', $this->expectedData)],
            [Request::create('/', 'PUT', $this->expectedData)],
            [Request::create('/', 'DELETE', $this->expectedData)]
        ];
    }

    /**
     * @dataProvider provideRequests
     */
    public function testGetData($request) {
        $input = new FormInput();
        $input->setRequest($request);
        $this->assertEquals($this->expectedData, $input->getData(), sprintf('Test with %s method', $request->getMethod()));
    }

    /**
     * @dataProvider provideRequests
     */
    public function testGetDataNamespaced($request) {
        $input = new FormInput();
        $input->setRequest($request);
        $this->assertEquals($this->expectedData['test-name'], $input->getData('test-name'), sprintf('Test with %s method', $request->getMethod()));
    }
}
