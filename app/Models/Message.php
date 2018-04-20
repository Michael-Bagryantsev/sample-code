<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Message
 * 
 * @property int $id
 * @property string $title
 * @property bool $is_sent
 * @property string $text
 * @property string $recipients
 * @property \Carbon\Carbon $sent_date
 * @property int $sent_user_id
 * 
 * @property \App\Models\AuthUser $auth_user
 * @property \Illuminate\Database\Eloquent\Collection $messages_attachments
 *
 * @package App\Models
 */
class Message extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'is_sent' => 'bool',
		'sent_user_id' => 'int'
	];

	protected $dates = [
		'sent_date'
	];

	protected $fillable = [
		'title',
		'is_sent',
		'text',
		'recipients',
		'sent_date',
		'sent_user_id'
	];

	public function auth_user()
	{
		return $this->belongsTo(\App\Models\AuthUser::class, 'sent_user_id');
	}

	public function messages_attachments()
	{
		return $this->hasMany(\App\Models\MessagesAttachment::class);
	}
}
