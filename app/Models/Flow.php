<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 12 Apr 2018 10:06:49 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Flow
 * 
 * @property int $id
 * @property int $condition_id
 * @property string $name
 * @property bool $percent
 * @property bool $is_active
 *
 * @package App\Models
 */
class Flow extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'condition_id' => 'int',
		'percent' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'condition_id',
		'name',
		'percent',
		'is_active'
	];
}
