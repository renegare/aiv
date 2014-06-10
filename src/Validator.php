<?php

namespace AIV;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class Validator implements \AIV\ValidatorInterface {

    /** @var InputInterface */
    protected $input;
    /** @var array */
    protected $contstraints;
    /** @var string */
    protected $name;
    /** @var ConstraintResolverInterface */
    protected $resolver;
    /** @var Symfony\Component\Validator\ConstraintViolationListInterface */
    protected $cachedValidation;
    /** @var array */
    protected $options;
    /** @var array */
    protected $cachedData;

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

        return $this->getRawData();
    }

    protected function getRawData() {
        if(!$this->cachedData) {
            $data = $this->input->getData($this->name);
            if($this->options['cache']) {
                $this->cachedData = $data;
            }
        } else {
            $data = $this->cachedData;
        }
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
        if(!$this->cachedValidation) {
            $data = $this->getRawData();
            try {
                $this->verifyNotEmpty($data);

                $constraints = [];

                foreach($this->constraints as $fieldName => $constraintsConfig) {
                    $fieldConstraints = [];
                    foreach($constraintsConfig as $constraintConfig) {
                        $fieldConstraints[] = $this->getContsraint($constraintConfig);
                    }

                    $constraints[$fieldName] = $fieldConstraints;
                }

                $constraints = new Collection([
                    'fields' => $constraints,
                    'allowExtraFields' => $this->options['allow.extra.params'],
                    'allowMissingFields' => $this->options['allow.missing.params']
                ]);

                $validator = Validation::createValidator();
                $cacheValidation = $validator->validateValue($data, $constraints);
            } catch (EmptyDataException $e) {
                $violation = new ConstraintViolation('Data is empty', 'Data is empty', [], $data, '', $data);
                $cacheValidation = new ConstraintViolationList([$violation]);
            }

            if($this->options['cache']) {
                $this->cacheValidation = $cacheValidation;
            }
        } else {
            $cacheValidation = $this->cacheValidation;
        }
        return $cacheValidation;
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

    /**
     * {@inheritdoc}
     */
    public function getErrors() {
        return $this->validate();
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options) {
        $this->options = array_merge([
            'cache' => false,
            'allow.extra.params' => false,
            'allow.missing.params' => false
        ], $options);
    }

    /**
     *
     */
    public function verifyNotEmpty($data) {
        if(!is_array($data) || count($data) < 1) {
            throw new EmptyDataException('Empty data!');
        }

        return true;
    }
}
