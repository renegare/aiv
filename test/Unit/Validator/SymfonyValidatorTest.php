<?php

namespace AIV\Test\Unit\Validator;

use AIV\Test\BaseTestCase;
use AIV\Validator\SymfonyValidator;

class SymfonyValidatorTest extends BaseTestCase {

    public function testHasInput() {
        $mockInput = $this->getMock('AIV\InputInterface');

        $validator = new SymfonyValidator();
        $this->assertFalse($validator->hasInput());
        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasInput());
    }

    public function testHasErrors() {
        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([
                'name' => 'John Smith'
            ]));

        $validator = new SymfonyValidator();
        $validator->setConstraints([
            'name' => ['required']
        ]);
        $validator->setInput($mockInput);
        $this->assertFalse($validator->hasErrors());
    }

    public function xtestHasData() {
        $this->assertTrue(false);
    }

    public function xtestHasDataException() {
        $this->assertTrue(false);
    }
}
