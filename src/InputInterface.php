<?php

namespace AIV;

interface InputInterface {

    /**
     * returns raw data. Holds logic on where to get the data from e.g $_POST
     * throw exception
     * @return array
     */
    public function getData();
}
