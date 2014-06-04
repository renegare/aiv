<?php

namespace AIV\Test\Unit;

use AIV\Test\BaseTestCase;
use AIV\Validator;

class ValidatorTest extends BaseTestCase {

    public function testHasInput() {
        $mockInput = $this->getMock('AIV\InputInterface');

        $validator = new Validator();
        $this->assertFalse($validator->hasInput());
        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasInput());
    }

    public function testHasErrors() {
        $validator = new Validator();
        $validator->setName('test-name');
        $validator->setConstraints([
            'name' => [
                'not.blank',
                ['type' => 'length', 'options' => ['min' => 2, 'max' => '20']]],
            'email' => ['not.blank', 'email']
        ]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnCallback(function($name){
                $this->assertEquals('test-name', $name);
                return [
                    'name' => 'John Smith',
                    'email' => 'user@renegare.com'];
            }));
        $validator->setInput($mockInput);
        $this->assertFalse($validator->hasErrors());

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnCallback(function($name){
                $this->assertEquals('test-name', $name);
                return ['name' => 'John Smith'];
            }));
        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasErrors());
    }

    public function testGetData() {
        $validData = [
            'name' => 'John Smith',
            'email' => 'user@renegare.com'
        ];

        $validator = new Validator();

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

        $validator = new Validator();

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

        $validator = new Validator();

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

    /**
     * test constraint finder is used
     */
    public function testSetConstraintFinder() {
        $constraint = new \Symfony\Component\Validator\Constraints\NotBlank();

        $validData = [
            'name' => 'John Smith'
        ];

        $mockConstraintResolver = $this->getMock('AIV\ConstraintResolverInterface');
        $mockConstraintResolver->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($constraint));

        $validator = new Validator();
        $validator->setConstraintResolver($mockConstraintResolver);
        $validator->setConstraints(['name' => ['not.blank']]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($validData));
        $validator->setInput($mockInput);
        $this->assertFalse($validator->hasErrors());
        $this->assertEquals($validData, $validator->getData());
    }
}
