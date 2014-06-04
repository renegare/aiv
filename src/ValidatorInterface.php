<?php

namespace AIV;

interface ValidatorInterface {

    public function hasErrors();

    public function hasInput();

    public function setInput(InputInterface $input);

    public function getData();
}
