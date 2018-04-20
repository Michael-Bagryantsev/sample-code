<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 12 Apr 2018 10:37:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class FlowsCondition
 * 
 * @property int $id
 * @property string $condition_name
 * @property string $condition_class
 *
 * @package App\Models
 */
class FlowsCondition extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'condition_name',
		'condition_class'
	];
}
