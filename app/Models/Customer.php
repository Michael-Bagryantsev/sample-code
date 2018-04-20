<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:08:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Customer
 * 
 * @property int $id
 * @property string $customer_from
 * @property int $amo_id
 * @property int $amo_lead_id
 * @property string $telegram_chat_id
 * @property string $telegram_username
 * @property int $billing_user_id
 * @property string $billing_token
 * @property int $plan_id
 * @property int $currency_id
 * @property string $customer_name
 * @property string $customer_chat_status
 * @property int $vip_till
 * @property int $added_time
 * @property int $lead_created
 * @property int $lead_manager
 * @property int $lead_completed
 *
 * @package App\Models
 */
class Customer extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'amo_id' => 'int',
		'amo_lead_id' => 'int',
		'billing_user_id' => 'int',
		'plan_id' => 'int',
		'currency_id' => 'int',
		'vip_till' => 'int',
		'added_time' => 'int',
		'lead_created' => 'int',
		'lead_manager' => 'int',
		'lead_completed' => 'int'
	];

	protected $hidden = [
		'billing_token'
	];

	protected $fillable = [
		'customer_from',
		'amo_id',
		'amo_lead_id',
		'telegram_chat_id',
		'telegram_username',
		'billing_user_id',
		'billing_token',
		'plan_id',
		'currency_id',
		'customer_name',
		'customer_chat_status',
		'vip_till',
		'added_time',
		'lead_created',
		'lead_manager',
		'lead_completed'
	];
}
