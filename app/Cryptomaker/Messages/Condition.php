<?php

namespace App\Cryptomaker\Messages;


class Condition
{
    protected $params = array();

    public function __construct(array $params = array())
    {
        $this->params = $params;

        return;
    }

    public function getConditionHtml()
    {
        return '';
    }

    public function checkCondition(array $params)
    {
        return true;
    }


}