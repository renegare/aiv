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
}
