<?php

namespace AIV\Validator;

use AIV\InputInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraint;

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
        if($this->hasErrors()) {
            throw new \RuntimeException('Cannot return data, as data is invalid!');
        }
        $data = $this->input->getData();
        return $data;
    }

    public function setConstraints(array $constraints) {
        $this->constraints = $constraints;
    }

    /**
     * the hear of validation here (Note: keeps no state):
     */
    protected function validate() {
        $data = $this->input->getData();

        $constraints = [];

        foreach($this->constraints as $fieldName => $constraintsConfig) {
            $fieldConstraints = [];
            foreach($constraintsConfig as $constraintConfig) {
                $fieldConstraints[] = $this->getContsraint($constraintConfig);
            }

            $constraints[$fieldName] = $fieldConstraints;
        }

        $constraints = new Collection($constraints);
        $validator = Validation::createValidator();
        return $validator->validateValue($data, $constraints);
    }

    public function getContsraint($constraintConfig) {
        if($constraintConfig instanceof Constraint) {
            $constraint = $constraintConfig;
        } else {
            if(is_array($constraintConfig)) {
                $class = $constraintConfig['type'];
                $options = $constraintConfig['options'];
            } else {
                $class = $constraintConfig;
                $options = null;
            }

            $_class = array_map(function($part){
                return ucfirst($part);
            }, explode('.', $class));
            $_class = 'Symfony\Component\Validator\Constraints\\' . implode('', $_class);
            $class = class_exists($_class)? $_class : $class;

            $constraint = new $class($options);
        }

        return $constraint;
    }
}
