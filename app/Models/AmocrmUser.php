<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AmocrmUser
 * 
 * @property int $id
 * @property int $uid
 * @property bool $active
 * @property bool $is_manager
 * @property string $name
 * @property string $last_name
 * @property string $login
 * @property int $stat_got_customers_today
 * @property int $stat_lost_customers_today
 * 
 * @property \Illuminate\Database\Eloquent\Collection $users_fbs
 *
 * @package App\Models
 */
class AmocrmUser extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'uid' => 'int',
		'active' => 'bool',
		'is_manager' => 'bool',
		'stat_got_customers_today' => 'int',
		'stat_lost_customers_today' => 'int'
	];

	protected $fillable = [
		'uid',
		'active',
		'is_manager',
		'name',
		'last_name',
		'login',
		'stat_got_customers_today',
		'stat_lost_customers_today'
	];

	public function users_fbs()
	{
		return $this->hasMany(\App\Models\UsersFb::class, 'amocrm_manager_id');
	}
}
