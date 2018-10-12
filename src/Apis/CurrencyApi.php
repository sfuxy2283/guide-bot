<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018-08-23
 * Time: 오후 8:30
 */

namespace Linebot\Apis;


class CurrencyApi
{
    static function getCurrencyData() {

        $access_key = getenv('CURRENCY_KEY');

        $koreaData = json_decode(file_get_contents("http://apilayer.net/api/live?access_key={$access_key}&currencies=KRW&source=USD&format=1"), true);
        $koreaRate = round($koreaData['quotes']['USDKRW'], 2);

        $japanData = json_decode(file_get_contents('http://apilayer.net/api/live?access_key=2f4842c82fc6e3097090734090b19c46&currencies=JPY&source=USD&format=1'), true);
        $japanRate = round($japanData['quotes']['USDJPY'], 2);

        $exchangeRate = $koreaRate / $japanRate;

        return $exchangeRate;
    }
}