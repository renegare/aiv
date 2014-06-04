<?php

namespace AIV;

class Manager {

    /** @var array of ValidatorInterface */
    protected $validators = [];
    /** @var InputInterface */
    protected $input;

    /**
     * add/register an input validator instance. If two or more validators are
     * registerd with the same name, an error is thrown
     * @param string $name - user defined name for the validator instance
     * @param InputValidatorInterface $validator
     * @throws LogicException - when two instances are registered with the same name
     * @return void
     */
    public function addInputValidator($name, ValidatorInterface $validator) {
        $this->hasValidator($name, function($name){
            throw new \LogicException(sprintf("You can only register one validator with the name '%s'", $name));
        }, null);

        $this->validators[$name] = $validator;
    }

    /**
     * set source of the input to validate
     * @param InputInterface $input
     * @return void
     */
    public function setInput(InputInterface $input) {
        $this->input = $input;
    }

    /**
     * @param string $name - name of registered validator
     * @throws OutOfRangeException - when requested validor does not exist
     * @return boolean
     */
    public function hasErrors($name) {
        return $this->getValidator($name)
            ->hasErrors();
    }

    /**
     * @param string $name - name of registered validator
     * @throws OutOfRangeException - when requested validor does not exist
     * @return array
     */
    public function getData($name) {
        return $this->getValidator($name)
            ->getData();
    }

    /**
     * get validator registerd with provided name. Sets input if one is available
     * @param string $name - name of registered validator
     * @throws OutOfRangeException - when requested validor does not exist
     * @return boolean
     */
    public function getValidator($name) {
        $this->hasValidator($name, null, function($name){
            throw new \OutOfRangeException(sprintf("No validator with the name '%s' has been registered", $name));
        });

        $validator = $this->validators[$name];
        if($this->input && !$validator->hasInput()) {
            $validator->setInput($this->input);
        }
        return $validator;
    }

    public function hasValidator($name, \Closure $success=null, \Closure $failure=null) {
        $value = isset($this->validators[$name]);

        if($value) {
            $value = $success? $success($name) : $value;
        } else {
            $value = $failure? $failure($name) : $value;
        }

        return $value;
    }
}
