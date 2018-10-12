<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018-08-25
 * Time: 오후 8:52
 */

namespace Linebot\Apis;


class WeatherApi
{
    static function getWeatherData($location)
    {
        $access_key = getenv('WEATHER_API_KEY');

        $data = file_get_contents("https://api.openweathermap.org/data/2.5/forecast?lang=ja&lat={$location['latitude']}&lon={$location['longitude']}&units=metric&APPID={$access_key}");

        $rawData = json_decode($data, true)['list'];

        $tomorrow = date('Y-m-d', time() + (24 * 60 * 60));
        $theDatAfterTomorrow = date('Y-m-d', time() + (2* 24 * 60 * 60));

        $climateData =[];

        $climateData['current'] = [
            'temp' => $rawData[0]['main']['temp'],
            'weather' => $rawData[0]['weather'][0]['description']
        ];

        foreach ($rawData as $value) {

            if ($value['dt_txt'] == "$tomorrow 09:00:00") {
                $climateData['tomorrowAm'] = [
                    'temp' => $value['main']['temp'],
                    'weather' => $value['weather'][0]['description']
                ];
            }

            if ($value['dt_txt'] == "$tomorrow 18:00:00") {
                $climateData['tomorrowPm'] = [
                    'temp' => $value['main']['temp'],
                    'weather' => $value['weather'][0]['description']
                ];
            }

            if ($value['dt_txt'] == "$theDatAfterTomorrow 09:00:00") {
                $climateData['afterTomorrowAm'] = [
                    'temp' => $value['main']['temp'],
                    'weather' => $value['weather'][0]['description']
                ];
            }

            if ($value['dt_txt'] == "$theDatAfterTomorrow 18:00:00") {
                $climateData['afterTomorrowPm'] = [
                    'temp' => $value['main']['temp'],
                    'weather' => $value['weather'][0]['description']
                ];
            }
        }

        return $climateData;
    }

}