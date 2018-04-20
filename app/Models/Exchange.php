<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Exchange
 * 
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $api_key
 * @property string $api_password
 * @property string $api_secret
 * 
 * @property \Illuminate\Database\Eloquent\Collection $currencies
 * @property \Illuminate\Database\Eloquent\Collection $orders
 * @property \Illuminate\Database\Eloquent\Collection $signals
 *
 * @package App\Models
 */
class Exchange extends Eloquent
{
	public $timestamps = false;

	protected $hidden = [
		'api_password',
		'api_secret'
	];

	protected $fillable = [
		'name',
		'description',
		'api_key',
		'api_password',
		'api_secret'
	];

	public function currencies()
	{
		return $this->hasMany(\App\Models\Currency::class);
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
