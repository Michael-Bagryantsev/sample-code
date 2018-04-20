<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class StatUsersFb
 * 
 * @property int $id
 * @property \Carbon\Carbon $date
 * @property int $new_users
 * @property int $chat_users
 * @property \Carbon\Carbon $last_calculate
 *
 * @package App\Models
 */
class StatUsersFb extends Eloquent
{
	protected $table = 'stat_users_fb';
	public $timestamps = false;

	protected $casts = [
		'new_users' => 'int',
		'chat_users' => 'int'
	];

	protected $dates = [
		'date',
		'last_calculate'
	];

	protected $fillable = [
		'date',
		'new_users',
		'chat_users',
		'last_calculate'
	];
}
