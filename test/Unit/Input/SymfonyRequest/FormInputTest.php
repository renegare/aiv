<?php

namespace AIV\Test\Unit\Input\SymfonyRequest;

use AIV\Test\BaseTestCase;
use AIV\Input\SymfonyRequest\FormInput;

class FormInputTest extends BaseTestCase {

    public function testPurpose() {
        $expectedData = [
            'test-name' => ['...'],
            'something-else' => ['...incorrect...']
        ];

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expectedData));

        $input = new FormInput();
        $input->setRequest($request);
        $this->assertEquals($expectedData, $input->getData('test-name'));
    }
}
