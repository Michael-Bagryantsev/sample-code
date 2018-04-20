<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 19 Apr 2018 06:57:44 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class FlowsButtonsAction
 * 
 * @property int $id
 * @property int $button_id
 * @property int $action_id
 * @property string $action_data
 *
 * @package App\Models
 */
class FlowsButtonsAction extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'button_id' => 'int',
		'action_id' => 'int'
	];

	protected $fillable = [
		'button_id',
		'action_id',
		'action_data'
	];
}
