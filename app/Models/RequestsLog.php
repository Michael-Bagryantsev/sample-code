<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:08:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class RequestsLog
 * 
 * @property \Carbon\Carbon $request_date
 * @property int $requests_count
 *
 * @package App\Models
 */
class RequestsLog extends Eloquent
{
	protected $table = 'requests_log';
	protected $primaryKey = 'request_date';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'requests_count' => 'int'
	];

	protected $dates = [
		'request_date'
	];

	protected $fillable = [
		'requests_count'
	];
}
