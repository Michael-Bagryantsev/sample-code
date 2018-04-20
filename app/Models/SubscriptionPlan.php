<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SubscriptionPlan
 * 
 * @property int $id
 * @property int $button_id
 * @property string $plan_name
 * @property float $plan_price
 * @property string $plan_duration
 * @property string $plan_excerpt
 * @property string $plan_description
 * @property int $plan_order
 *
 * @package App\Models
 */
class SubscriptionPlan extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'button_id' => 'int',
		'plan_price' => 'float',
		'plan_order' => 'int'
	];

	protected $fillable = [
		'button_id',
		'plan_name',
		'plan_price',
		'plan_duration',
		'plan_excerpt',
		'plan_description',
		'plan_order'
	];
}
