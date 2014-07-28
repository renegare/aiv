<?php

namespace AIV;

interface ValidatorInterface {

    /**
     * checkes if provided input contains any errors
     * @return boolean
     */
    public function hasErrors();

    /**
     * checkes if an input has been set
     * @return boolean
     */
    public function hasInput();

    /**
     * set input data source
     * @param InputInterface $input
     * @return void
     */
    public function setInput(InputInterface $input);

    /**
     * returns validated data. If no data or has error(s), then should
     * throw exception
     * @throws RuntimeException
     * @return array
     */
    public function getData();

    /**
     * allow for user defined short hand constrant config to be handled by an
     * external resolver. set it here
     * @param ContraintResolverInterface $resolver
     * @return void
     */
    public function setConstraintResolver(ConstraintResolverInterface $resolver);

    /**
     * allow for user defined short hand constrant config to be handled by an
     * external resolver. set it here
     * @return Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function getErrors();

    /**
     * set options for how the validator validates
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * set namespace of incoming data to validate
     * @param array $options
     */
    public function setNamespace($namespace);
}
