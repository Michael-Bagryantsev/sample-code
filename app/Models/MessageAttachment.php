<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MessageAttachment
 * 
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string $file
 * @property string $url
 * @property string $fb_attachment_id
 * @property int $created_user_id
 * 
 * @property \App\Models\AuthUser $auth_user
 * @property \Illuminate\Database\Eloquent\Collection $messages_attachments
 *
 * @package App\Models
 */
class MessageAttachment extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'created_user_id' => 'int'
	];

	protected $fillable = [
		'title',
		'type',
		'file',
		'url',
		'fb_attachment_id',
		'created_user_id'
	];

	public function auth_user()
	{
		return $this->belongsTo(\App\Models\AuthUser::class, 'created_user_id');
	}

	public function messages_attachments()
	{
		return $this->hasMany(\App\Models\MessagesAttachment::class, 'messageattachment_id');
	}
}
