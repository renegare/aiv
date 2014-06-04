<?php

namespace AIV\Test\Unit;

use AIV\Test\BaseTestCase;
use AIV\Manager;

class ManagerTest extends BaseTestCase {

    public function testInputValidation() {
        $manager = new Manager();
        $inputData = [
            'name' => 'John Smith',
            'unexpected' => 'data'];
        $hasInput = false;
        $mockInput = $this->getMock('AIV\InputInterface');
        $mockValidator = $this->getMock('AIV\ValidatorInterface');
        $mockValidator->expects($this->exactly(2))
            ->method('hasInput')
            ->will($this->returnCallback(function() use (&$hasInput){
                return $hasInput;
            }));
        $mockValidator->expects($this->once())
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

        $manager->addInputValidator('test-form', $mockValidator, true);
        $manager->setInput($mockInput);
        $this->assertFalse($manager->hasErrors('test-form'));
        $this->assertEquals($inputData, $manager->getData('test-form'));
    }
}
