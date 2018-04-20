<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BillingOrder
 * 
 * @property int $id
 * @property \Carbon\Carbon $date_created
 * @property \Carbon\Carbon $date_expiration
 * @property string $status
 * @property float $amount
 * @property string $address
 * @property string $sync_id
 * @property int $currency_id
 * @property int $customer_id
 * @property int $plan_id
 * 
 * @property \App\Models\BillingCurrency $billing_currency
 * @property \App\Models\BillingCustomer $billing_customer
 * @property \App\Models\BillingPlan $billing_plan
 *
 * @package App\Models
 */
class BillingOrder extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'amount' => 'float',
		'currency_id' => 'int',
		'customer_id' => 'int',
		'plan_id' => 'int'
	];

	protected $dates = [
		'date_created',
		'date_expiration'
	];

	protected $fillable = [
		'date_created',
		'date_expiration',
		'status',
		'amount',
		'address',
		'sync_id',
		'currency_id',
		'customer_id',
		'plan_id'
	];

	public function billing_currency()
	{
		return $this->belongsTo(\App\Models\BillingCurrency::class, 'currency_id');
	}

	public function billing_customer()
	{
		return $this->belongsTo(\App\Models\BillingCustomer::class, 'customer_id');
	}

	public function billing_plan()
	{
		return $this->belongsTo(\App\Models\BillingPlan::class, 'plan_id');
	}
}
