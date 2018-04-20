<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BotAnswersButton
 * 
 * @property int $id
 * @property int $answer_id
 * @property int $next_answer_id
 * @property string $button_text
 * @property int $button_order
 *
 * @package App\Models
 */
class BotAnswersButton extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'answer_id' => 'int',
		'next_answer_id' => 'int',
		'button_order' => 'int'
	];

	protected $fillable = [
		'answer_id',
		'next_answer_id',
		'button_text',
		'button_order'
	];
}
