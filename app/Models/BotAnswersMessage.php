<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BotAnswersMessage
 * 
 * @property int $id
 * @property int $answer_id
 * @property string $message_text
 * @property string $message_method
 * @property int $message_order
 *
 * @package App\Models
 */
class BotAnswersMessage extends Eloquent
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
