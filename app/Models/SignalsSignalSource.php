<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SignalsSignalSource
 * 
 * @property int $id
 * @property int $signal_id
 * @property int $signalsource_id
 * 
 * @property \App\Models\SignalSource $signal_source
 * @property \App\Models\Signal $signal
 *
 * @package App\Models
 */
class SignalsSignalSource extends Eloquent
{
	protected $table = 'signals_signal_source';
	public $timestamps = false;

	protected $casts = [
		'signal_id' => 'int',
		'signalsource_id' => 'int'
	];

	protected $fillable = [
		'signal_id',
		'signalsource_id'
	];

	public function signal_source()
	{
		return $this->belongsTo(\App\Models\SignalSource::class, 'signalsource_id');
	}

	public function signal()
	{
		return $this->belongsTo(\App\Models\Signal::class);
	}
}
