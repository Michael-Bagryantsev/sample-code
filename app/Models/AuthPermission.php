<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuthPermission
 * 
 * @property int $id
 * @property string $name
 * @property int $content_type_id
 * @property string $codename
 * 
 * @property \App\Models\DjangoContentType $django_content_type
 * @property \Illuminate\Database\Eloquent\Collection $auth_group_permissions
 * @property \Illuminate\Database\Eloquent\Collection $auth_user_user_permissions
 *
 * @package App\Models
 */
class AuthPermission extends Eloquent
{
	protected $table = 'auth_permission';
	public $timestamps = false;

	protected $casts = [
		'content_type_id' => 'int'
	];

	protected $fillable = [
		'name',
		'content_type_id',
		'codename'
	];

	public function django_content_type()
	{
		return $this->belongsTo(\App\Models\DjangoContentType::class, 'content_type_id');
	}

	public function auth_group_permissions()
	{
		return $this->hasMany(\App\Models\AuthGroupPermission::class, 'permission_id');
	}

	public function auth_user_user_permissions()
	{
		return $this->hasMany(\App\Models\AuthUserUserPermission::class, 'permission_id');
	}
}
