<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PixelFbEvent
 * 
 * @property int $id
 * @property string $title
 * 
 * @property \Illuminate\Database\Eloquent\Collection $pixel_fb_logs
 *
 * @package App\Models
 */
class PixelFbEvent extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'title'
	];

	public function pixel_fb_logs()
	{
		return $this->hasMany(\App\Models\PixelFbLog::class, 'event_id');
	}
}
