<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 12 Apr 2018 10:07:02 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class FlowsAnswersMessage
 * 
 * @property int $id
 * @property int $answer_id
 * @property string $message_text
 * @property string $message_method
 * @property int $message_order
 *
 * @package App\Models
 */
class FlowsAnswersMessage extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'answer_id' => 'int',
		'message_order' => 'int'
	];

	protected $fillable = [
		'answer_id',
		'message_text',
		'message_method',
		'message_order'
	];
}
