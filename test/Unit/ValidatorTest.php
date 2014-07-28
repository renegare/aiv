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
        $validator->setConstraints([
            'name' => [$notBlankConstraint, $lengthConstraint],
            'email' => [$notBlankConstraint, $emailConstraint]
        ]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnCallback(function($name){
                $this->assertEquals(null, $name);
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
                $this->assertEquals(null, $name);
                return ['name' => 'John Smith'];
            }));
        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasErrors());
    }

    public function testHasErrorsNamespaced() {
        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();
        $emailConstraint = new \Symfony\Component\Validator\Constraints\Email();
        $lengthConstraint = new \Symfony\Component\Validator\Constraints\Length([
            'min' => 2,
            'max' => 20
            ]);

        $validator = new Validator();
        $validator->setNamespace('test-name');
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

    public function testGetErrors() {

        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();
        $emailConstraint = new \Symfony\Component\Validator\Constraints\Email();
        $lengthConstraint = new \Symfony\Component\Validator\Constraints\Length([
            'min' => 2,
            'max' => 20
            ]);

        $validator = new Validator();
        $validator->setConstraints([
            'name' => [$notBlankConstraint, $lengthConstraint],
            'email' => [$notBlankConstraint, $emailConstraint]
        ]);


        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnCallback(function(){
                return ['name' => 'John Smith'];
            }));
        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasErrors());
        $errors = $validator->getErrors();
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintViolationListInterface', $errors);
        $this->count(1, $errors);
    }

    /**
     * by default the cache option is false, and therefore will fully validate
     * the data from the input every time. This can be to much for constraints that
     * require an external resource. The cache option will validate the data once
     * and cache the state thereafter
     */
    public function testCacheOption() {
        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnCallback(function(){
                return ['name' => 'John Smith'];
            }));

        $validator = new Validator();
        $validator->setOptions(['cache' => true]);
        $validator->setConstraints([
            'name' => [$notBlankConstraint]
        ]);
        $validator->setInput($mockInput);
        $validator->hasErrors();
        $validator->hasErrors();
        $validator->getErrors();
        $validator->getErrors();
        $validator->getData();
        $validator->getData();
    }

    public function testAllowExtraFieldsOption() {
        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();

        $expectedData = ['name' => 'John Smith', 'extra' => 'param'];
        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnCallback(function() use ($expectedData){
                return $expectedData;
            }));

        $validator = new Validator();
        $validator->setOptions(['allow.extra.params' => true]);
        $validator->setConstraints([
            'name' => [$notBlankConstraint]
        ]);
        $validator->setInput($mockInput);
        $this->assertEquals($expectedData, $validator->getData());
    }

    public function testAllowMissingParamsOption() {
        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();

        $expectedData = ['name' => 'John Smith'];
        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->any())
            ->method('getData')
            ->will($this->returnCallback(function() use ($expectedData){
                return $expectedData;
            }));

        $validator = new Validator();
        $validator->setOptions(['allow.missing.params' => true]);
        $validator->setConstraints([
            'name' => [$notBlankConstraint],
            'email' => [$notBlankConstraint]
        ]);
        $validator->setInput($mockInput);
        $this->assertEquals($expectedData, $validator->getData());
    }

    /**
     * AIV considers data to be empty if it fails one of the criterias:
     * * must be an array
     * * size must be greater than 0
     */
    public function provideDataConsideredAsEmpty(){
        return [
            [null],
            [[]],
            [''],
            ['yes its not empty, but its also not an array!']
        ];
    }
    /**
     * @dataProvider provideDataConsideredAsEmpty
     */
    public function testEmptyDataError($emptyData) {

        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();

        $validator = new Validator();
        $validator->setConstraints(['name' => [$notBlankConstraint]]);

        $mockInput = $this->getMock('AIV\InputInterface');
        $mockInput->expects($this->once())
            ->method('getData')
            ->will($this->returnCallback(function() use ($emptyData){
                return $emptyData;
            }));

        $validator->setInput($mockInput);
        $this->assertTrue($validator->hasErrors());
    }
}
