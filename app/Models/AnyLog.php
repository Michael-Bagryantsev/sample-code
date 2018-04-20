<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AnyLog
 * 
 * @property int $id
 * @property string $log
 * @property int $date_added
 *
 * @package App\Models
 */
class AnyLog extends Eloquent
{
	protected $table = 'any_log';
	public $timestamps = false;

	protected $casts = [
		'date_added' => 'int'
	];

	protected $fillable = [
		'log',
		'date_added'
	];
}
