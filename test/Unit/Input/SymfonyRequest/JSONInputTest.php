<?php

namespace AIV\Test\Unit\Input\SymfonyRequest;

use AIV\Test\BaseTestCase;
use AIV\Input\SymfonyRequest\JSONInput;
use Symfony\Component\HttpFoundation\Request;

class JSONInputTest extends BaseTestCase {

    public function testPurpose() {
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
        $this->assertEquals($expectedData['namespace'], $input->getData('namespace'));
    }

    public function testReturnDataNoNameSpace() {
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
