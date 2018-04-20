<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DjangoMigration
 * 
 * @property int $id
 * @property string $app
 * @property string $name
 * @property \Carbon\Carbon $applied
 *
 * @package App\Models
 */
class DjangoMigration extends Eloquent
{
	public $timestamps = false;

	protected $dates = [
		'applied'
	];

	protected $fillable = [
		'app',
		'name',
		'applied'
	];
}
