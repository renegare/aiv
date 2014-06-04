<?php

namespace AIV\Input;

use Symfony\Component\HttpFoundation\Request;

class SymfonyRequestInput implements \AIV\InputInterface {

    protected $request;

    /**
     * set request data source
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($name) {
        return $this->request->get($name);
    }
}
