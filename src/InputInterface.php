<?php

namespace AIV;

interface InputInterface {

    /**
     * returns raw data. Holds logic on where to get the data from e.g $_POST
     * @param string $name - namespace of the data to get else return the whole payload
     * @return array - always return an array (empty when no data is found)
     */
    public function getData($name = null);
}
