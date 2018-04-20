<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 27 Mar 2018 07:11:08 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BillingCustomer
 * 
 * @property int $id
 * @property \Carbon\Carbon $date_created
 * @property \Carbon\Carbon $date_paid_till
 * @property string $first_name
 * @property string $last_name
 * @property string $billing_token
 * @property int $billing_user_id
 * 
 * @property \Illuminate\Database\Eloquent\Collection $billing_orders
 * @property \Illuminate\Database\Eloquent\Collection $users_fbs
 *
 * @package App\Models
 */
class BillingCustomer extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'billing_user_id' => 'int'
	];

	protected $dates = [
		'date_created',
		'date_paid_till'
	];

	protected $hidden = [
		'billing_token'
	];

	protected $fillable = [
		'date_created',
		'date_paid_till',
		'first_name',
		'last_name',
		'billing_token',
		'billing_user_id'
	];

	public function billing_orders()
	{
		return $this->hasMany(\App\Models\BillingOrder::class, 'customer_id');
	}

	public function users_fbs()
	{
		return $this->hasMany(\App\Models\UsersFb::class, 'customer_id');
	}
}
