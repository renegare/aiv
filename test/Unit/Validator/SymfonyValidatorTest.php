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
        $validator = new SymfonyValidator();

        $validator->setConstraints([
            'name' => [
                'not.blank',
                ['type' => 'length', 'options' => ['min' => 2, 'max' => '20']]],
            'email' => ['not.blank', 'email']
        ]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([
                'name' => 'John Smith',
                'email' => 'user@renegare.com'
            ]));
        $validator->setInput($mockInput);
        $this->assertFalse($validator->hasErrors());

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([
                'name' => 'John Smith'
            ]));
        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasErrors());
    }

    public function testGetData() {
        $validData = [
            'name' => 'John Smith',
            'email' => 'user@renegare.com'
        ];

        $validator = new SymfonyValidator();

        $validator->setConstraints([
            'name' => [
                'not.blank',
                ['type' => 'length', 'options' => ['min' => 2, 'max' => '20']]],
            'email' => ['not.blank', 'email']
        ]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($validData));
        $validator->setInput($mockInput);
        $this->assertFalse($validator->hasErrors());
        $this->assertEquals($validData, $validator->getData());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetDataException() {
        $validData = [
            'name' => 'John Smith'
        ];

        $validator = new SymfonyValidator();

        $validator->setConstraints([
            'name' => [
                'not.blank',
                ['type' => 'length', 'options' => ['min' => 2, 'max' => '20']]],
            'email' => ['not.blank', 'email']
        ]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($validData));
        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasErrors());
        $validator->getData();
    }

    /**
     * assert custom constraint instances are accepted
     */
    public function testCustomConstraintInstances() {
        $emailConstraint = new \Symfony\Component\Validator\Constraints\Email();

        $validData = [
            'name' => 'John Smith',
            'email' => 'user@renegare.com'
        ];

        $validator = new SymfonyValidator();

        $validator->setConstraints([
            'name' => [
                'not.blank',
                ['type' => 'length', 'options' => ['min' => 2, 'max' => '20']]],
            'email' => ['not.blank', $emailConstraint]
        ]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($validData));
        $validator->setInput($mockInput);
        $this->assertFalse($validator->hasErrors());
        $this->assertEquals($validData, $validator->getData());
    }
}
