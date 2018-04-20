<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 19 Apr 2018 06:57:31 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class FlowsAction
 * 
 * @property int $id
 * @property string $action_name
 * @property string $action_class
 *
 * @package App\Models
 */
class FlowsAction extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'action_name',
		'action_class'
	];
}
