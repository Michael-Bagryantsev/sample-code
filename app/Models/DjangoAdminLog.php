<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DjangoAdminLog
 * 
 * @property int $id
 * @property \Carbon\Carbon $action_time
 * @property string $object_id
 * @property string $object_repr
 * @property int $action_flag
 * @property string $change_message
 * @property int $content_type_id
 * @property int $user_id
 * 
 * @property \App\Models\DjangoContentType $django_content_type
 * @property \App\Models\AuthUser $auth_user
 *
 * @package App\Models
 */
class DjangoAdminLog extends Eloquent
{
	protected $table = 'django_admin_log';
	public $timestamps = false;

	protected $casts = [
		'action_flag' => 'int',
		'content_type_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'action_time'
	];

	protected $fillable = [
		'action_time',
		'object_id',
		'object_repr',
		'action_flag',
		'change_message',
		'content_type_id',
		'user_id'
	];

	public function django_content_type()
	{
		return $this->belongsTo(\App\Models\DjangoContentType::class, 'content_type_id');
	}

	public function auth_user()
	{
		return $this->belongsTo(\App\Models\AuthUser::class, 'user_id');
	}
}
