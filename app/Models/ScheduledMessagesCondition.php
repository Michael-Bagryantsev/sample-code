<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ScheduledMessagesCondition
 * 
 * @property int $id
 * @property string $condition_name
 * @property string $return_method
 *
 * @package App\Models
 */
class ScheduledMessagesCondition extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'condition_name',
		'condition_class'
	];
}
