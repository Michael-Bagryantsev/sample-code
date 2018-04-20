<?php

namespace App\Helpers;

use Irazasyed\LaravelGAMP\Facades\GAMP;
use stdClass;
use Telegram\Bot\Api;

class AnalyticsHelper
{
    private static $maxLen = 32;
    private static $defaultOhid = 'DEFAULT';

    /**
     * @param array $params
     * client_id - telegram chatId
     * page - message text
     *
     * trying to parse url
     * https://telegram.me/cmpremiumbot?start=plan:GreatPlan|ref:viktorsads|ohid:abcd|source:googleads
     */
    public static function logStartRequest(array $params)
    {
        $analyticsInfo = new stdClass();

        $gamp = GAMP::setClientId($params['client_id']);
        $gamp->setDocumentPath('/' . trim(preg_replace('/\s+/', '-', strtolower($params['page'])), '/'));

        $query = trim(substr($params['page'], 6));
        if (strlen($query) > 0) {
            $chunks = array_chunk(preg_split('/(-|\_)/', $query), 2);
            if (sizeof($chunks[0]) === 2) {
                $result = array_combine(array_column($chunks, 0), array_column($chunks, 1));

                foreach ($result as $key => $value) {
                    if (!empty($value)) {
                        switch ($key) {
                            case 'plan':
                                $analyticsInfo->plan = $value;
                                break;
                            case 'ref':
                                $analyticsInfo->ref = $value;
                                $gamp->setCampaignMedium($analyticsInfo->ref);
                                break;
                            case 'ohid':
                                $analyticsInfo->ohid = $value;
                                break;
                            case 'source':
                                $analyticsInfo->source = $value;
                                $gamp->setCampaignSource($analyticsInfo->source);
                                break;
                            default:
                                $telegram = new Api(config('logging.telegram_token'));
                                $telegram->sendMessage(['chat_id' => config('logging.telegram_chat_id'),
                                    'text' => 'Unexpected key/value pair in start command: ' . $key . '/' . $value]);
                                break;
                        }
                    }
                }
            } else {
                if (!empty($chunks[0][1])) {
                    $analyticsInfo->source = $chunks[0][1];
                    $gamp->setCampaignSource($analyticsInfo->source);
                }
            }
        }
        if (!isset($analyticsInfo->ohid)) {
            $analyticsInfo->ohid = self::$defaultOhid;
        }

        $gamp->setCampaignKeyword($analyticsInfo->ohid);
        $gamp->sendPageview();

        return $analyticsInfo;
    }

    /**
     * @param array $params
     * client_id - telegram chatId
     * page - message text
     *
     * trying to parse url
     * https://telegram.me/cmpremiumbot?start=plan:GreatPlan|ref:viktorsads|ohid:abcd|source:googleads
     */
    public static function logRequest(array $params)
    {
        if (!isset($params['page'])) {
            $params['page'] = 'unknown';
        }
        $params['page'] = self::filterString($params['page']);

        $gamp = GAMP::setClientId($params['client_id']);
        $gamp->setDocumentPath('/' . trim(preg_replace('/\s+/', '-', strtolower($params['page'])), '/'));

        $gamp->sendPageview();

        return true;
    }

    /**
     *  Remove all not alphanumeric + maxlength = self::$maxLen
     * dont filter before /start, since we have params
     */
    public static function filterString(string $string, int $length = null)
    {
        if (is_null($length)) {
            $length = self::$maxLen;
        }
        $result = preg_replace('/[^\da-z]/i', '', $string);
        $result = substr($result, 0, $length);

        return $result;
    }

}