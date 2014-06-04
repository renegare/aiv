<?php

namespace AIV;

interface InputInterface {

    /**
     * returns raw data. Holds logic on where to get the data from e.g $_POST
     * @param string $name - namespace of the data to get
     * @return array
     */
    public function getData($name);
}
