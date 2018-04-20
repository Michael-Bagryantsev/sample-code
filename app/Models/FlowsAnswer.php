<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 12 Apr 2018 10:06:54 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class FlowsAnswer
 * 
 * @property int $id
 * @property int $flow_id
 * @property string $answer_title
 *
 * @package App\Models
 */
class FlowsAnswer extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
        'flow_id' => 'int',
	];

	protected $fillable = [
		'flow_id',
		'answer_title',
        'pos_data',
	];
}
