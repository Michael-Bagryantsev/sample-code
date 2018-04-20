<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuthUserGroup
 * 
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 * 
 * @property \App\Models\AuthGroup $auth_group
 * @property \App\Models\AuthUser $auth_user
 *
 * @package App\Models
 */
class AuthUserGroup extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'group_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'group_id'
	];

	public function auth_group()
	{
		return $this->belongsTo(\App\Models\AuthGroup::class, 'group_id');
	}

	public function auth_user()
	{
		return $this->belongsTo(\App\Models\AuthUser::class, 'user_id');
	}
}
