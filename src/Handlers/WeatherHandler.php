<?php
/**
 * Handler the weather requst
 * 
 * PHP 7.2.7
 */

namespace Linebot\Handlers;

use Linebot\Apis\WeatherApi;

class WeatherHandler extends CoreHandler
{
    private $location;

    /**
     * To get the weather information from the api, the bot needs location data , so
     * first get the location data from the user and using it send weather data to user
     */
    public function handle()
    {
        $message = $this->isTextMessage();

        if ($message === '終了' || $message === '종료') {
            return $this->endWeatherMode();
        }

        // Check location data from the user and the data is valide
        $this->location = $this->userData->getLocationData();

        if (! $this->location) {

            $this->location = $this->isLocationData();

            if (! $this->location) {

                $this->sendLocationMessage();
            }

            $this->userData->setLocationData($this->location);
        }

        // The location data is valid, send weather data to user
        $this->sendWeatherData($this->location);
    }

    /**
     * Check the message from the user is location or not
     * 
     * @return boll, string
     */
    private function isLocationData()
    {
        if (! array_key_exists('message', $this->event)) {

            return false;

        }

        $message = $this->event['message'];

        if ($message['type'] != 'location') {
            return false;
        }

        $location = [];
        $location['title'] = $message['title'];
        $location['address'] = $message['address'];
        $location['latitude'] = $message['latitude'];
        $location['longitude'] = $message['longitude'];

        return $location;
    }

    /**
     * Check the message from the user is text or not
     * 
     * @return boll, string
     */
    private function isTextMessage()
    {
        if (! array_key_exists('message', $this->event)) {

            return false;

        }

        $message = $this->event['message'];

        if (! array_key_exists('text', $message)) {
            return false;
        }

        $text = $message['text'];

        return $text;

    }

    /**
     * Send the message to user to ask location data
     * 
     * @return null
     */
    private function sendLocationMessage()
    {
        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "下のボタンを押して位置を入力してください。
終了をご希望の場合、「終了」を入力してください",
                    'quickReply' => [
                        'items' => [
                            [
                                'type' => 'action',
                                'action' => [
                                    'type' => 'location',
                                    'label' => '位置入力'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    /**
     * Using loaction data from the user, fetch weather data from api and
     * send it to user
     */
    private function sendWeatherData($location)
    {
        // Fetch data from api
        $weatherData = WeatherApi::getWeatherData($location);

        $weatherMessage = array();

        if (empty($weatherData)) {

            $weatherMessage[] = [
                'type' => 'text',
                'text' => "{$location['title']}の検索結果がありません"
            ];

        } else {

            // Extract current weather from the response
            if (array_key_exists('current', $weatherData)) {

                $weatherMessage[] = [
                    'type' => 'text',
                    'text' => "今の{$location['title']}の天気
{$weatherData['current']['temp']}C {$weatherData['current']['weather']}"
                ];

            }

            // Extract tomorrow weather from the response
            if (array_key_exists('tomorrowAm', $weatherData) && array_key_exists('tomorrowPm', $weatherData)) {

                $weatherMessage[] = [
                    'type' => 'text',
                    'text' => "明日の天気
午前: {$weatherData['tomorrowAm']['temp']}C, {$weatherData['tomorrowAm']['weather']}
午後: {$weatherData['tomorrowPm']['temp']}C, {$weatherData['tomorrowPm']['weather']}"
                ];

            }

            // Extract the day after tomorrow weather from the response
            if (array_key_exists('afterTomorrowAm', $weatherData) && array_key_exists('afterTomorrowPm', $weatherData)) {

                $weatherMessage[] = [
                    'type' => 'text',
                    'text' => "明後日の天気
午前: {$weatherData['afterTomorrowAm']['temp']}C, {$weatherData['afterTomorrowAm']['weather']}
午後: {$weatherData['afterTomorrowPm']['temp']}C, {$weatherData['afterTomorrowPm']['weather']}"
                ];
            }
        }

        // Send weather information to user
        $message = [
            'replyToken' => $this->replyToken,
            'messages' => $weatherMessage
        ];

        $this->bot->replyMessage($message);

        $this->userData->modeTurnOff('weather');
        $this->userData->deleteLocationData();

    }

    private function endWeatherMode()
    {

        $this->userData->modeTurnOff('weather');

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '電気モドが終了しました'
                ]
            ]
        ];

        $this->bot->replyMessage($message);

        $this->userData->deleteLocationData();
    }


}