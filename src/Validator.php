<?php

namespace AIV;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraint;

class Validator implements \AIV\ValidatorInterface {

    /** @var InputInterface */
    protected $input;
    /** @var array */
    protected $contstraints;
    /** @var string */
    protected $name;
    /** @var ConstraintResolverInterface */
    protected $resolver;
    /**
     * {@inheritdoc}
     */
    public function hasErrors() {
        $errors = $this->getErrors();
        return count($errors) > 0;
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
        $data = $this->input->getData($this->name);
        return $data;
    }

    public function setConstraints(array $constraints) {
        $this->constraints = $constraints;
    }

    /**
     * the hear of validation here (Note: keeps no state)
     * @return Symfony\Component\Validator\ConstraintViolationListInterface
     */
    protected function validate() {
        $data = $this->input->getData($this->name);

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

    /**
     * takes a constraint configuration string|array or a Constraint instance and return
     * a Constraint
     * @param mixed $constraintConfig
     * @return Symfony\Component\Validator\Constraint
     */
    public function getContsraint($constraintConfig) {
        if($constraintConfig instanceof Constraint) {
            $constraint = $constraintConfig;
        } else {
            $constraint = $this->getConstraintResolver()
                ->resolve($constraintConfig);
        }

        return $constraint;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setConstraintResolver(ConstraintResolverInterface $resolver) {
        $this->resolver = $resolver;
    }

    public function getConstraintResolver() {
        if(!$this->resolver) {
            throw new \LogicException('You need to set a Constraint Resolver in order to handle custom constraints');
        }
        return $this->resolver;
    }

    public function getErrors() {
        return $this->validate();
    }
}
