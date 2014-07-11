<?php

namespace AIV\Input\SymfonyRequest;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractInput implements \AIV\InputInterface {

    protected $request;

    /**
     * set request data source
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }
}
