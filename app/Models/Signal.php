<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Signal
 * 
 * @property int $id
 * @property \Carbon\Carbon $date
 * @property string $text
 * @property string $term
 * @property float $price_buy
 * @property float $price_close
 * @property bool $is_published
 * @property \Carbon\Carbon $profit_max_date
 * @property int $added_user_id
 * @property int $currency_id
 * @property float $price_max
 * @property bool $is_proof_published
 * @property float $target1
 * @property float $target2
 * @property float $target3
 * @property float $price_stoploss
 * @property string $comment
 * @property int $exchange_id
 * @property float $price_buy_high
 * 
 * @property \App\Models\AuthUser $auth_user
 * @property \App\Models\Currency $currency
 * @property \App\Models\Exchange $exchange
 * @property \Illuminate\Database\Eloquent\Collection $orders
 * @property \Illuminate\Database\Eloquent\Collection $signals_signal_sources
 *
 * @package App\Models
 */
class Signal extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'price_buy' => 'float',
		'price_close' => 'float',
		'is_published' => 'bool',
		'added_user_id' => 'int',
		'currency_id' => 'int',
		'price_max' => 'float',
		'is_proof_published' => 'bool',
		'target1' => 'float',
		'target2' => 'float',
		'target3' => 'float',
		'price_stoploss' => 'float',
		'exchange_id' => 'int',
		'price_buy_high' => 'float'
	];

	protected $dates = [
		'date',
		'profit_max_date'
	];

	protected $fillable = [
		'date',
		'text',
		'term',
		'price_buy',
		'price_close',
		'is_published',
		'profit_max_date',
		'added_user_id',
		'currency_id',
		'price_max',
		'is_proof_published',
		'target1',
		'target2',
		'target3',
		'price_stoploss',
		'comment',
		'exchange_id',
		'price_buy_high'
	];

	public function auth_user()
	{
		return $this->belongsTo(\App\Models\AuthUser::class, 'added_user_id');
	}

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class);
	}

	public function exchange()
	{
		return $this->belongsTo(\App\Models\Exchange::class);
	}

	public function orders()
	{
		return $this->hasMany(\App\Models\Order::class);
	}

	public function signals_signal_sources()
	{
		return $this->hasMany(\App\Models\SignalsSignalSource::class);
	}
}
