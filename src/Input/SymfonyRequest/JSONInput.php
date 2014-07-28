<?php

namespace AIV\Input\SymfonyRequest;

class JSONInput extends FormInput {

    /**
     * {@inheritdoc}
     */
    public function getData($name = null) {
        $request = $this->request;
        $dataSrc = 'request';

        switch(strtolower($request->getMethod())) {
            case 'get':
                return parent::getData($name);
                break;
        }

        $json = $this->request->getContent();
        $data = @json_decode($json, true);

        if($name !== null && isset($data[$name])) {
            $data = $data[$name];
        }

        if(!is_array($data)) {
            $data = [];
        }

        return $data;
    }

}
