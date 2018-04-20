<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 27 Mar 2018 07:12:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class UsersFb
 * 
 * @property int $id
 * @property int $fbuid
 * @property string $first_name
 * @property string $last_name
 * @property \Carbon\Carbon $date_registered
 * @property int $customer_id
 * @property bool $is_vip
 * @property string $ref
 * @property int $amocrm_contact_id
 * @property int $amocrm_lead_id
 * @property int $amocrm_manager_id
 * @property \Carbon\Carbon $amocrm_start_flow
 * @property int $amocrm_pair_attempts
 * @property bool $got_first_message
 * @property \Carbon\Carbon $amocrm_set_time_manager
 * @property string $utm_source
 * @property int $last_fb_ad_id
 * @property int $last_fb_ad_id_additional
 * @property string $locale
 * @property string $sex
 * @property int $timezone
 * @property bool $live_talking
 * @property \Carbon\Carbon $date_first_message
 * @property \Carbon\Carbon $date_last_message
 * @property bool $is_in_stop_list
 * @property int $sequence_id
 * @property string $customer_from
 * @property int $plan_id
 * @property int $currency_id
 * @property int $telegram_chat_id
 * @property string $telegram_username
 * @property string $customer_chat_status
 * @property \Carbon\Carbon $lead_created
 * @property \Carbon\Carbon $lead_manager
 * @property \Carbon\Carbon $lead_completed
 * 
 * @property \App\Models\AmocrmUser $amocrm_user
 * @property \App\Models\BillingCustomer $billing_customer
 * @property \App\Models\ManychatSequence $manychat_sequence
 *
 * @package App\Models
 */
class UsersFb extends Eloquent
{
	protected $table = 'users_fb';
	public $timestamps = false;

	protected $casts = [
		'fbuid' => 'int',
		'customer_id' => 'int',
		'is_vip' => 'bool',
        'is_blocked' => 'bool',
		'amocrm_contact_id' => 'int',
		'amocrm_lead_id' => 'int',
		'amocrm_manager_id' => 'int',
		'amocrm_pair_attempts' => 'int',
		'got_first_message' => 'bool',
		'last_fb_ad_id' => 'int',
		'last_fb_ad_id_additional' => 'int',
		'timezone' => 'int',
		'live_talking' => 'bool',
		'is_in_stop_list' => 'bool',
		'sequence_id' => 'int',
		'plan_id' => 'int',
		'currency_id' => 'int',
		'telegram_chat_id' => 'int'
	];

	protected $dates = [
		'date_registered',
		'amocrm_start_flow',
		'amocrm_set_time_manager',
		'date_first_message',
		'date_last_message',
		'lead_created',
		'lead_manager',
		'lead_completed'
	];

	protected $fillable = [
		'fbuid',
		'first_name',
		'last_name',
		'date_registered',
		'customer_id',
		'is_vip',
        'is_blocked',
		'ohid',
		'amocrm_contact_id',
		'amocrm_lead_id',
		'amocrm_manager_id',
		'amocrm_start_flow',
		'amocrm_pair_attempts',
		'got_first_message',
		'amocrm_set_time_manager',
		'utm_source',
		'last_fb_ad_id',
		'last_fb_ad_id_additional',
		'locale',
		'sex',
		'timezone',
		'live_talking',
		'date_first_message',
		'date_last_message',
		'is_in_stop_list',
		'sequence_id',
		'customer_from',
		'plan_id',
		'currency_id',
		'telegram_chat_id',
		'telegram_username',
		'customer_chat_status',
		'lead_created',
		'lead_manager',
		'lead_completed',
        'utm_channel',
        'utm_plan'
	];

	public static function getVip()
    {
        return self::where('is_vip', 1)->get();
    }

    public static function getPublic()
    {
        return self::where('is_vip', 0)->get();
    }

    public static function getAll()
    {
        return self::all();
    }

    public static function getLast3DaysRegistered()
    {
        return self::where('date_registered', '>', date('Y-m-d', time() - 3*24*60*60))->get();
    }

    public static function getLastWeekRegistered()
    {
        return self::where('date_registered', '>', date('Y-m-d', time() - 7*24*60*60))->get();
    }

    public static function getLastMonthRegistered()
    {
        return self::where('date_registered', '>', date('Y-m-d', strtotime('-1 month')))->get();
    }

	public function amocrm_user()
	{
		return $this->belongsTo(\App\Models\AmocrmUser::class, 'amocrm_manager_id');
	}

	public function billing_customer()
	{
		return $this->belongsTo(\App\Models\BillingCustomer::class, 'customer_id');
	}

	public function manychat_sequence()
	{
		return $this->belongsTo(\App\Models\ManychatSequence::class, 'sequence_id');
	}

	public function getName()
    {
	    return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAmoChatSenderId()
    {
        return 'U2-' . $this->telegram_chat_id;
    }
}
