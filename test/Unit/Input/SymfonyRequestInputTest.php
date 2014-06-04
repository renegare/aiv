<?php

namespace AIV\Test\Unit\Input;

use AIV\Test\BaseTestCase;
use AIV\Input\SymfonyRequestInput;

class SymfonyRequestInputTest extends BaseTestCase {

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

        $input = new SymfonyRequestInput();
        $input->setRequest($request);
        $this->assertEquals($expectedData, $input->getData('test-name'));
    }
}
