<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018-08-27
 * Time: 오후 6:22
 */

namespace Linebot\Apis;


class MapApi
{
    static function getPlaceData($location, $keyWord)
    {
        $apiKey = getenv('MAP_API_KEY');

        $data = file_get_contents("https://maps.googleapis.com/maps/api/place/nearbysearch/json?location={$location['latitude']},{$location['longitude']}&radius=1000&keyword={$keyWord}&language=ja&key={$apiKey}");

        $placeData = json_decode($data, true)['results'];

        return $placeData;
    }

}