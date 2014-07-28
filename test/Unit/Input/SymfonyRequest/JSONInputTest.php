<?php

namespace AIV\Test\Unit\Input\SymfonyRequest;

use AIV\Test\BaseTestCase;
use AIV\Input\SymfonyRequest\JSONInput;
use Symfony\Component\HttpFoundation\Request;

class JSONInputTest extends BaseTestCase {

    protected $expectedData = [
        'test-name' => ['...'],
        'something-else' => ['...incorrect...']
    ];

    public function provideRequests() {
        $expectedJSONData = json_encode($this->expectedData);
        return [
            [Request::create('/', 'GET', $this->expectedData)],
            [Request::create('/', 'POST', [], [], [], [], $expectedJSONData)],
            [Request::create('/', 'PUT', [], [], [], [], $expectedJSONData)],
            [Request::create('/', 'DELETE', [], [], [], [], $expectedJSONData)]
        ];
    }

    /**
     * @dataProvider provideRequests
     */
    public function testGetData($request) {
        $input = new JSONInput();
        $input->setRequest($request);
        $this->assertEquals($this->expectedData, $input->getData());
    }

    /**
     * @dataProvider provideRequests
     */
    public function testGetDataNamespaced($request) {
        $input = new JSONInput();
        $input->setRequest($request);
        $this->assertEquals($this->expectedData['test-name'], $input->getData('test-name'));
    }

    public function xtestReturnDataNoNameSpace() {
        $expectedData = [
            'namespace' => [
                'test-name' => ['...'],
                'something-else' => ['...incorrect...']
            ]
        ];

        $jsonBody = json_encode($expectedData);
        $request = Request::create('/', 'POST', [], [], [], [], $jsonBody);

        $input = new JSONInput();
        $input->setRequest($request);
        $this->assertEquals($expectedData, $input->getData());
    }

    public function provideInvalidJSONData() {
        return [
            [null],
            ['not_an_array'],
            [json_encode('not_an_encoded_array')]
        ];
    }

    /**
     * @dataProvider provideInvalidJSONData
     */
    public function testReturnArrayForInvalidJSON($jsonBody) {

        $request = Request::create('/', 'POST', [], [], [], [], $jsonBody);

        $input = new JSONInput();
        $input->setRequest($request);
        $this->assertEquals([], $input->getData());
    }
}
