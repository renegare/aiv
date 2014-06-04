<?php

namespace AIV;

interface ConstraintResolverInterface {

    /**
     * @param mixed $config - some configuration that will resolve to a Constraint
     * @throws BadArgumentsException - config makes no sense|invalid
     * @return Symfony\Component\Validator\Constraint
     */
    public function resolve($config);
}
