<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 27 Mar 2018 07:11:27 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BillingCurrency
 * 
 * @property int $id
 * @property string $code
 * @property string $title
 * @property int $zeros_after_comma
 * @property int $button_id
 * 
 * @property \Illuminate\Database\Eloquent\Collection $billing_orders
 * @property \Illuminate\Database\Eloquent\Collection $billing_plans
 *
 * @package App\Models
 */
class BillingCurrency extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'zeros_after_comma' => 'int',
		'button_id' => 'int'
	];

	protected $fillable = [
		'code',
		'title',
		'zeros_after_comma',
		'button_id'
	];

	public function billing_orders()
	{
		return $this->hasMany(\App\Models\BillingOrder::class, 'currency_id');
	}

	public function billing_plans()
	{
		return $this->hasMany(\App\Models\BillingPlan::class, 'currency_id');
	}
}
