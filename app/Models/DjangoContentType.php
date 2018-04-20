<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DjangoContentType
 * 
 * @property int $id
 * @property string $app_label
 * @property string $model
 * 
 * @property \Illuminate\Database\Eloquent\Collection $auth_permissions
 * @property \Illuminate\Database\Eloquent\Collection $django_admin_logs
 *
 * @package App\Models
 */
class DjangoContentType extends Eloquent
{
	protected $table = 'django_content_type';
	public $timestamps = false;

	protected $fillable = [
		'app_label',
		'model'
	];

	public function auth_permissions()
	{
		return $this->hasMany(\App\Models\AuthPermission::class, 'content_type_id');
	}

	public function django_admin_logs()
	{
		return $this->hasMany(\App\Models\DjangoAdminLog::class, 'content_type_id');
	}
}
