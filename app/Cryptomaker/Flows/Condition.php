<?php

namespace App\Cryptomaker\Flows;

use DB;

class Condition
{

    public static function getConditionHtml()
    {
        return '';
    }

    public static function checkCondition(array $params = [])
    {
        return true;
    }

    public static function getFlow()
    {
        $classInfo = new \ReflectionClass(static::class);

        $flows = DB::table('flows')
            ->selectRaw('flows.*')
            ->join('flows_conditions', 'flows.condition_id', '=', 'flows_conditions.id')
            ->where('flows_conditions.condition_class', $classInfo->getShortName())
            ->where('flows.is_active', 1)
            ->get();

        if (empty($flows)) {
            return false;
        } else {
            $sum = 0;
            foreach ($flows as $flow) {
                $sum += $flow->percent;
            }

            $randomValue = rand(0, $sum);
            $sum = 0;
            foreach ($flows as $flow) {
                $sum += $flow->percent;
                if ($sum >= $randomValue) {
                    return $flow;
                }
            }
        }
    }

}