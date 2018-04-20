<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 09 Apr 2018 12:14:43 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class UsersVip
 * 
 * @property int $id
 * @property string $customer_from
 * @property int $users_fb_id
 * @property int $billing_plan_id
 * @property int $amocrm_contact_id
 * @property int $amocrm_lead_id
 * @property int $telegram_chat_id
 * @property string $telegram_username
 * @property int $telegram_user_id
 * @property string $telegram_role
 * @property int $telegram_access_hash
 * @property string $first_name
 * @property string $last_name
 * @property int $vip_from
 * @property int $vip_till
 * @property string $transaction_id
 * @property int $currency_id
 *
 * @package App\Models
 */
class UsersVip extends Eloquent
{
	protected $table = 'users_vip';
	public $timestamps = false;

	protected $casts = [
		'users_fb_id' => 'int',
		'billing_plan_id' => 'int',
		'amocrm_contact_id' => 'int',
		'amocrm_lead_id' => 'int',
		'telegram_chat_id' => 'int',
		'telegram_user_id' => 'int',
		'telegram_access_hash' => 'int',
		'vip_from' => 'int',
		'vip_till' => 'int',
		'currency_id' => 'int'
	];

	protected $fillable = [
		'customer_from',
		'users_fb_id',
		'billing_plan_id',
		'amocrm_contact_id',
		'amocrm_lead_id',
		'telegram_chat_id',
		'telegram_username',
		'telegram_user_id',
		'telegram_role',
		'telegram_access_hash',
		'first_name',
		'last_name',
		'vip_from',
		'vip_till',
		'transaction_id',
		'currency_id'
	];
}
