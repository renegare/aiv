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
     * set namespace of the data to get from the input
     * @param string $name
     */
    public function setName($name);

    /**
     * allow for user defined short hand constrant config to be handled by an
     * external resolver. set it here
     * @param ContraintResolverInterface $resolver
     * @return void
     */
    public function setConstraintResolver(ConstraintResolverInterface $resolver);
}
