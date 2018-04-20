<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SignalSource
 * 
 * @property int $id
 * @property string $name
 * @property int $karma
 * @property string $link
 * @property bool $paid
 * 
 * @property \Illuminate\Database\Eloquent\Collection $signals
 *
 * @package App\Models
 */
class SignalSource extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'karma' => 'int',
		'paid' => 'bool'
	];

	protected $fillable = [
		'name',
		'karma',
		'link',
		'paid'
	];

	public function signals()
	{
		return $this->belongsToMany(\App\Models\Signal::class, 'signals_signal_source', 'signalsource_id')
					->withPivot('id');
	}
}
