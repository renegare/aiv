<?php

namespace AIV\Input\SymfonyRequest;

class FormInput extends AbstractInput {

    /**
     * {@inheritdoc}
     */
    public function getData($name = null) {
        $request = $this->request;
        $dataSrc = 'request';

        switch(strtolower($request->getMethod())) {
            case 'get':
                $dataSrc = 'query';
                break;
        }
        if($name) {
            $data = $request->$dataSrc->get($name);
        } else {
            $data = $request->$dataSrc->all();
        }

        return $data;
    }
}
