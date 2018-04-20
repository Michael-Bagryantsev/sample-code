<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuthGroupPermission
 * 
 * @property int $id
 * @property int $group_id
 * @property int $permission_id
 * 
 * @property \App\Models\AuthPermission $auth_permission
 * @property \App\Models\AuthGroup $auth_group
 *
 * @package App\Models
 */
class AuthGroupPermission extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'group_id' => 'int',
		'permission_id' => 'int'
	];

	protected $fillable = [
		'group_id',
		'permission_id'
	];

	public function auth_permission()
	{
		return $this->belongsTo(\App\Models\AuthPermission::class, 'permission_id');
	}

	public function auth_group()
	{
		return $this->belongsTo(\App\Models\AuthGroup::class, 'group_id');
	}
}
