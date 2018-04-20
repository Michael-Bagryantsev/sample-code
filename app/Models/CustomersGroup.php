<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CustomersGroup
 * 
 * @property int $id
 * @property string $group_name
 * @property string $return_method
 *
 * @package App\Models
 */
class CustomersGroup extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'group_name',
		'return_method'
	];
}
