<?php

namespace AIV\Test\Unit;

use AIV\Test\BaseTestCase;
use AIV\Manager;

class ManagerTest extends BaseTestCase {

    public function testPurpose() {
        $inputData = [
            'name' => 'John Smith',
            'unexpected' => 'data'];
        $hasInput = false;
        $mockInput = $this->getMock('AIV\InputInterface');
        $mockValidator = $this->getMock('AIV\ValidatorInterface');

        $mockValidator->expects($this->atLeastOnce())
            ->method('setInput')
            ->will($this->returnCallback(function($input) use ($mockInput, &$hasInput){
                $this->assertSame($input, $mockInput);
                $hasInput = true;
            }));
        $mockValidator->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnCallback(function(){
                return false;
            }));
        $mockValidator->expects($this->once())
            ->method('getData')
            ->will($this->returnCallback(function() use ($inputData){
                return $inputData;
            }));

        $manager = new Manager();
        $manager->addValidator('test-form', $mockValidator);
        $manager->setInput($mockInput);
        $this->assertFalse($manager->hasErrors('test-form'));
        $this->assertEquals($inputData, $manager->getData('test-form'));
    }

    /**
     * test that adding two validators with the same name throws an exception
     * @expectedException LogicException
     */
    public function testAddInputValidatorsException() {
        $mockValidator = $this->getMock('AIV\ValidatorInterface');

        $manager = new Manager();
        $manager->addValidator('test-form', $mockValidator);
        $manager->addValidator('test-form', $mockValidator);
    }

    /**
     * test that trying to get a validator that does not exist throws an exception
     * @expectedException LogicException
     */
    public function testGetValidatorException() {
        $manager = new Manager();
        $manager->getValidator('non-existent');
    }

    public function testGetErrors() {
        $mockValidator = $this->getMock('AIV\ValidatorInterface');
        $mockValidator->expects($this->once())
            ->method('getErrors');

        $manager = new Manager();
        $manager->addValidator('test-form', $mockValidator);
        $manager->setInput($this->getMock('AIV\InputInterface'));
        $manager->getErrors('test-form');
    }
}
