<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuthUser
 * 
 * @property int $id
 * @property string $password
 * @property \Carbon\Carbon $last_login
 * @property bool $is_superuser
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property bool $is_staff
 * @property bool $is_active
 * @property \Carbon\Carbon $date_joined
 * 
 * @property \Illuminate\Database\Eloquent\Collection $auth_user_groups
 * @property \Illuminate\Database\Eloquent\Collection $auth_user_user_permissions
 * @property \Illuminate\Database\Eloquent\Collection $django_admin_logs
 * @property \Illuminate\Database\Eloquent\Collection $message_attachments
 * @property \Illuminate\Database\Eloquent\Collection $messages
 * @property \Illuminate\Database\Eloquent\Collection $orders
 * @property \Illuminate\Database\Eloquent\Collection $signals
 *
 * @package App\Models
 */
class AuthUser extends Eloquent
{
	protected $table = 'auth_user';
	public $timestamps = false;

	protected $casts = [
		'is_superuser' => 'bool',
		'is_staff' => 'bool',
		'is_active' => 'bool'
	];

	protected $dates = [
		'last_login',
		'date_joined'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'password',
		'last_login',
		'is_superuser',
		'username',
		'first_name',
		'last_name',
		'email',
		'is_staff',
		'is_active',
		'date_joined'
	];

	public function auth_user_groups()
	{
		return $this->hasMany(\App\Models\AuthUserGroup::class, 'user_id');
	}

	public function auth_user_user_permissions()
	{
		return $this->hasMany(\App\Models\AuthUserUserPermission::class, 'user_id');
	}

	public function django_admin_logs()
	{
		return $this->hasMany(\App\Models\DjangoAdminLog::class, 'user_id');
	}

	public function message_attachments()
	{
		return $this->hasMany(\App\Models\MessageAttachment::class, 'created_user_id');
	}

	public function messages()
	{
		return $this->hasMany(\App\Models\Message::class, 'sent_user_id');
	}

	public function orders()
	{
		return $this->hasMany(\App\Models\Order::class, 'user_created_id');
	}

	public function signals()
	{
		return $this->hasMany(\App\Models\Signal::class, 'added_user_id');
	}
}
