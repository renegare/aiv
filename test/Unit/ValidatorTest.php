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
        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();
        $emailConstraint = new \Symfony\Component\Validator\Constraints\Email();
        $lengthConstraint = new \Symfony\Component\Validator\Constraints\Length([
            'min' => 2,
            'max' => 20
            ]);

        $validator = new Validator();
        $validator->setName('test-name');
        $validator->setConstraints([
            'name' => [$notBlankConstraint, $lengthConstraint],
            'email' => [$notBlankConstraint, $emailConstraint]
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
        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();
        $emailConstraint = new \Symfony\Component\Validator\Constraints\Email();
        $lengthConstraint = new \Symfony\Component\Validator\Constraints\Length([
            'min' => 2,
            'max' => 20
            ]);

        $validData = [
            'name' => 'John Smith',
            'email' => 'user@renegare.com'
        ];

        $validator = new Validator();

        $validator->setConstraints([
            'name' => [$notBlankConstraint, $lengthConstraint],
            'email' => [$notBlankConstraint, $emailConstraint]
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
        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();
        $emailConstraint = new \Symfony\Component\Validator\Constraints\Email();
        $lengthConstraint = new \Symfony\Component\Validator\Constraints\Length([
            'min' => 2,
            'max' => 20
            ]);

        $validData = [
            'name' => 'John Smith'
        ];

        $validator = new Validator();

        $validator->setConstraints([
            'name' => [$notBlankConstraint, $lengthConstraint],
            'email' => [$notBlankConstraint, $emailConstraint]
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
     * test constraint resolver is used for custom constraint config
     */
    public function testSetConstraintResolver() {
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

    /**
     * @expectedException LogicException
     */
    public function testSetConstraintResolverException() {
        $validData = [
            'name' => 'John Smith'
        ];

        $validator = new Validator();
        $validator->setConstraints(['name' => ['not.blank']]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($validData));
        $validator->setInput($mockInput);
        $validator->hasErrors();
    }
}
