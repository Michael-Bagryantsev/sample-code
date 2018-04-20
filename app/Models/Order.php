<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Order
 * 
 * @property \Carbon\Carbon $date_created
 * @property int $id
 * @property string $sync_id
 * @property string $status
 * @property float $value
 * @property float $price_open
 * @property float $price_close
 * @property float $price_stoploss
 * @property string $comment
 * @property int $currency_id
 * @property int $exchange_id
 * @property int $parent_order_id
 * @property int $signal_id
 * @property \Carbon\Carbon $date_done
 * @property string $type
 * @property int $user_created_id
 * 
 * @property \App\Models\Currency $currency
 * @property \App\Models\Exchange $exchange
 * @property \App\Models\Order $order
 * @property \App\Models\Signal $signal
 * @property \App\Models\AuthUser $auth_user
 * @property \Illuminate\Database\Eloquent\Collection $orders
 *
 * @package App\Models
 */
class Order extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'value' => 'float',
		'price_open' => 'float',
		'price_close' => 'float',
		'price_stoploss' => 'float',
		'currency_id' => 'int',
		'exchange_id' => 'int',
		'parent_order_id' => 'int',
		'signal_id' => 'int',
		'user_created_id' => 'int'
	];

	protected $dates = [
		'date_created',
		'date_done'
	];

	protected $fillable = [
		'date_created',
		'sync_id',
		'status',
		'value',
		'price_open',
		'price_close',
		'price_stoploss',
		'comment',
		'currency_id',
		'exchange_id',
		'parent_order_id',
		'signal_id',
		'date_done',
		'type',
		'user_created_id'
	];

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class);
	}

	public function exchange()
	{
		return $this->belongsTo(\App\Models\Exchange::class);
	}

	public function order()
	{
		return $this->belongsTo(\App\Models\Order::class, 'parent_order_id');
	}

	public function signal()
	{
		return $this->belongsTo(\App\Models\Signal::class);
	}

	public function auth_user()
	{
		return $this->belongsTo(\App\Models\AuthUser::class, 'user_created_id');
	}

	public function orders()
	{
		return $this->hasMany(\App\Models\Order::class, 'parent_order_id');
	}
}
