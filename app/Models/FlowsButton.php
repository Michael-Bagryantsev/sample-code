<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 12 Apr 2018 10:07:08 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class FlowsButton
 * 
 * @property int $id
 * @property int $flow_id
 * @property int $message_id
 * @property int $next_answer_id
 * @property string $button_text
 * @property int $button_order
 *
 * @package App\Models
 */
class FlowsButton extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'flow_id' => 'int',
		'prev_answer_id' => 'int',
		'next_answer_id' => 'int',
		'button_order' => 'int'
	];

	protected $fillable = [
		'flow_id',
		'prev_answer_id',
		'next_answer_id',
		'button_text',
		'button_order'
	];
}
