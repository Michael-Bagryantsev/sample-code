<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CustomersChat
 * 
 * @property int $id
 * @property int $customer_id
 * @property string $message_from
 * @property string $message_from_name
 * @property string $message_text
 * @property int $message_time
 *
 * @package App\Models
 */
class CustomersChat extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int',
		'message_time' => 'int'
	];

	protected $fillable = [
		'customer_id',
		'message_from',
		'message_from_name',
		'message_text',
		'message_time'
	];
}
