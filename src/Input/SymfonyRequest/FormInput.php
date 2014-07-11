<?php

namespace AIV\Input\SymfonyRequest;

class FormInput extends AbstractInput {

    /**
     * {@inheritdoc}
     */
    public function getData($name = null) {
        return $this->request->get($name);
    }
}
