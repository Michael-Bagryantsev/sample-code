<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuthUserUserPermission
 * 
 * @property int $id
 * @property int $user_id
 * @property int $permission_id
 * 
 * @property \App\Models\AuthPermission $auth_permission
 * @property \App\Models\AuthUser $auth_user
 *
 * @package App\Models
 */
class AuthUserUserPermission extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'permission_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'permission_id'
	];

	public function auth_permission()
	{
		return $this->belongsTo(\App\Models\AuthPermission::class, 'permission_id');
	}

	public function auth_user()
	{
		return $this->belongsTo(\App\Models\AuthUser::class, 'user_id');
	}
}
