<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class StatsPerDay
 * 
 * @property int $id
 * @property \Carbon\Carbon $day
 * @property int $new_leads_manager_facebook
 * @property int $new_leads_manager_telegram
 * @property int $new_leads_bot_facebook
 * @property int $new_leads_bot_telegram
 * @property int $leads_completed_telegram
 * @property int $leads_completed_facebook
 *
 * @package App\Models
 */
class StatsPerDay extends Eloquent
{
	protected $table = 'stats_per_day';
	public $timestamps = false;

	protected $casts = [
		'new_leads_manager_facebook' => 'int',
		'new_leads_manager_telegram' => 'int',
		'new_leads_bot_facebook' => 'int',
		'new_leads_bot_telegram' => 'int',
		'leads_completed_telegram' => 'int',
		'leads_completed_facebook' => 'int'
	];

	protected $dates = [
		'day'
	];

	protected $fillable = [
		'day',
		'new_leads_manager_facebook',
		'new_leads_manager_telegram',
		'new_leads_bot_facebook',
		'new_leads_bot_telegram',
		'leads_completed_telegram',
		'leads_completed_facebook'
	];
}
