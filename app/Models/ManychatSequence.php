<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ManychatSequence
 * 
 * @property int $id
 * @property string $title
 * 
 * @property \Illuminate\Database\Eloquent\Collection $users_fbs
 *
 * @package App\Models
 */
class ManychatSequence extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'title'
	];

	public function users_fbs()
	{
		return $this->hasMany(\App\Models\UsersFb::class, 'sequence_id');
	}
}
