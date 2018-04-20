<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PixelFbLog
 * 
 * @property int $id
 * @property \Carbon\Carbon $date
 * @property int $fbuid
 * @property string $response
 * @property int $event_id
 * 
 * @property \App\Models\PixelFbEvent $pixel_fb_event
 *
 * @package App\Models
 */
class PixelFbLog extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'fbuid' => 'int',
		'event_id' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'date',
		'fbuid',
		'response',
		'event_id'
	];

	public function pixel_fb_event()
	{
		return $this->belongsTo(\App\Models\PixelFbEvent::class, 'event_id');
	}
}
