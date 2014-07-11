<?php

namespace AIV\Input\SymfonyRequest;

class JSONInput extends AbstractInput {

    /**
     * {@inheritdoc}
     */
    public function getData($name = null) {
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
