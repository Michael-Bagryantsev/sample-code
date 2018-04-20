<?php

namespace App\Cryptomaker\Messages;


class ScheduleTime extends Condition
{

    public function getConditionHtml()
    {
        return view('scheduled-messages/conditions/send-at-time');
    }

    public function checkCondition($params)
    {
        return strtotime($params->{'send-time'}) < time();
    }

}