<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DjangoSession
 * 
 * @property string $session_key
 * @property string $session_data
 * @property \Carbon\Carbon $expire_date
 *
 * @package App\Models
 */
class DjangoSession extends Eloquent
{
	protected $table = 'django_session';
	protected $primaryKey = 'session_key';
	public $incrementing = false;
	public $timestamps = false;

	protected $dates = [
		'expire_date'
	];

	protected $fillable = [
		'session_data',
		'expire_date'
	];
}
