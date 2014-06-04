<?php

namespace AIV;

class Manager {

    protected $validators = [];
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

    public function getValidator($name) {
        $validator = $this->validators[$name];
        if(!$validator->hasInput()) {
            $validator->setInput($this->input);
        }
        return $validator;
    }
}
