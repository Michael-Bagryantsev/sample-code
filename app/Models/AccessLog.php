<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Mar 2018 08:12:11 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AccessLog
 * 
 * @property int $id
 * @property string $access_from
 * @property string $access_post
 * @property string $access_get
 * @property string $access_raw_post
 * @property int $access_time
 *
 * @package App\Models
 */
class AccessLog extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'access_time' => 'int'
	];

	protected $fillable = [
		'access_from',
		'access_post',
		'access_get',
		'access_raw_post',
		'access_time'
	];

    public static function logRequest(string $accessFrom)
    {
        $inputJson = file_get_contents('php://input');

        $log = new self();
        $log->access_from = $accessFrom;
        $log->access_post = json_encode($_POST);
        $log->access_get = json_encode($_GET);
        $log->access_raw_post = $inputJson;
        $log->access_time = time();
        $log->save();

        return true;
    }
}
