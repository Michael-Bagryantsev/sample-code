<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MessagesAttachment
 * 
 * @property int $id
 * @property int $message_id
 * @property int $messageattachment_id
 * 
 * @property \App\Models\Message $message
 * @property \App\Models\MessageAttachment $message_attachment
 *
 * @package App\Models
 */
class MessagesAttachment extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'message_id' => 'int',
		'messageattachment_id' => 'int'
	];

	protected $fillable = [
		'message_id',
		'messageattachment_id'
	];

	public function message()
	{
		return $this->belongsTo(\App\Models\Message::class);
	}

	public function message_attachment()
	{
		return $this->belongsTo(\App\Models\MessageAttachment::class, 'messageattachment_id');
	}
}
