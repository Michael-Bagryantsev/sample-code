<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuthGroup
 * 
 * @property int $id
 * @property string $name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $auth_group_permissions
 * @property \Illuminate\Database\Eloquent\Collection $auth_user_groups
 *
 * @package App\Models
 */
class AuthGroup extends Eloquent
{
	protected $table = 'auth_group';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function auth_group_permissions()
	{
		return $this->hasMany(\App\Models\AuthGroupPermission::class, 'group_id');
	}

	public function auth_user_groups()
	{
		return $this->hasMany(\App\Models\AuthUserGroup::class, 'group_id');
	}
}
