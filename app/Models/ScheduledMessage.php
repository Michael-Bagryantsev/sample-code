<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ScheduledMessage
 * 
 * @property int $id
 * @property int $customers_group_id
 * @property int $condition_id
 * @property string $message_name
 * @property string $message_text
 * @property bool $is_active
 *
 * @package App\Models
 */
class ScheduledMessage extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'customers_group_id' => 'int',
		'condition_id' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'customers_group_id',
		'condition_id',
		'message_name',
        'message_text',
        'message_data',
		'is_active',
        'is_public_channel'
	];
}
