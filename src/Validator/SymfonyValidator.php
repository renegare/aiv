<?php

namespace AIV\Validator;

use AIV\InputInterface;

class SymfonyValidator implements \AIV\ValidatorInterface {

    /** @var InputInterface */
    protected $input;
    /** @var array */
    protected $contstraints;

    /**
     * {@inheritdoc}
     */
    public function hasErrors() {
        return count($this->validate()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function hasInput() {
        return $this->input !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function setInput(InputInterface $input) {
        $this->input = $input;
    }

    /**
     * {@inheritdoc}
     */
    public function getData() {
        throw new \Exception('Not Implemented');
    }

    public function setConstraints(array $constraints) {
        $this->constraints = $constraints;
    }

    /**
     * the hear of validation here (Note: keeps no state):
     */
    protected function validate() {
        throw new \Exception('Not Implemented');
        $data = $this->input->getData('name!?');

        // create validation constraint instances

        // validate aye!?
    }

}
