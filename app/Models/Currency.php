<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Currency
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $link
 * @property float $price
 * @property \Carbon\Carbon $price_updated
 * @property int $exchange_id
 * @property string $image_url
 * 
 * @property \App\Models\Exchange $exchange
 * @property \Illuminate\Database\Eloquent\Collection $orders
 * @property \Illuminate\Database\Eloquent\Collection $signals
 *
 * @package App\Models
 */
class Currency extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'price' => 'float',
		'exchange_id' => 'int'
	];

	protected $dates = [
		'price_updated'
	];

	protected $fillable = [
		'code',
		'name',
		'link',
		'price',
		'price_updated',
		'exchange_id',
		'image_url'
	];

	public function exchange()
	{
		return $this->belongsTo(\App\Models\Exchange::class);
	}

	public function orders()
	{
		return $this->hasMany(\App\Models\Order::class);
	}

	public function signals()
	{
		return $this->hasMany(\App\Models\Signal::class);
	}
}
