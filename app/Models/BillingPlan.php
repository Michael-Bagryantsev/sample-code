<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BillingPlan
 * 
 * @property int $id
 * @property string $title
 * @property string $code
 * @property float $price
 * @property int $prolong_hours
 * @property int $prolong_days
 * @property int $prolong_months
 * @property int $prolong_years
 * @property bool $is_public
 * @property \Carbon\Carbon $date_valid_until
 * @property \Carbon\Carbon $date_created
 * @property int $currency_id
 * @property string $amocrm_sync_title
 * @property int $manychat_sequence_id
 * 
 * @property \App\Models\BillingCurrency $billing_currency
 * @property \Illuminate\Database\Eloquent\Collection $billing_orders
 *
 * @package App\Models
 */
class BillingPlan extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'price' => 'float',
		'prolong_hours' => 'int',
		'prolong_days' => 'int',
		'prolong_months' => 'int',
		'prolong_years' => 'int',
		'is_public' => 'bool',
		'currency_id' => 'int',
		'manychat_sequence_id' => 'int'
	];

	protected $dates = [
		'date_valid_until',
		'date_created'
	];

	protected $fillable = [
		'title',
		'code',
		'price',
		'prolong_hours',
		'prolong_days',
		'prolong_months',
		'prolong_years',
		'is_public',
		'date_valid_until',
		'date_created',
		'currency_id',
		'amocrm_sync_title',
		'manychat_sequence_id'
	];

	public function billing_currency()
	{
		return $this->belongsTo(\App\Models\BillingCurrency::class, 'currency_id');
	}

	public function billing_orders()
	{
		return $this->hasMany(\App\Models\BillingOrder::class, 'plan_id');
	}

	public function getProlongTime($initTime = null)
    {
        if (is_null($initTime)) {
            $initTime = date('Y-m-d H:i:s');
        }

        $prolongTime = new DateTime($initTime);
        $prolongTime->add(new DateInterval('P' . $this->prolong_years . 'Y'
            . $this->prolong_months . 'M' . $this->prolong_days . 'DT' . $this->prolong_hours . 'H'));

        return $prolongTime->format('Y-m-d H:i:s');
    }
}
