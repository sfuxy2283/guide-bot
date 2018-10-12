<?php


namespace Linebot\Handlers;

use Linebot\Apis\MapApi;


class PlaceHandler extends CoreHandler
{
    private $location;
    private $keyWord;

    /**
     * To get the place information from the api, the bot needs 2 kinds of information, so
     * First get the location data from the user and get the keyword that user wants to search.
     */
    public function handle()
    {
        $message = $this->isTextMessage();

        // If the user wants quit place search quit the handler.
        if ($message === '終了' || $message === '종료') {
            return $this->endPlaceMode();
        }

        // Check location data from the user and the data is valide, then
        // send message to user to ask keyword.
        $this->location = $this->userData->getLocationData();

        if (! $this->location) {

            $this->location = $this->isLocationData();

            if (! $this->location) {

                $this->sendLocationMessage();
            }

            $this->userData->setLocationData($this->location);

            return $this->sendKeywordMessage();
        }

        // Check the keyword is valid.
        if (! $message) {
            return $this->sendKeywordMessage();
        }

        $this->keyWord = $message;

        // Location data and Keyword are valid send place datas to user.
        $this->sendPlaceData($this->location, $this->keyWord);
    }

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

    private function sendKeywordMessage()
    {
        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "検索するキーワードを入力してください。
終了をご希望の場合、「終了」を入力してください"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    /**
     * Call api and get place data and send it to user
     */
    private function sendPlaceData($location, $keyWord)
    {
        $placeData = MapApi::getPlaceData($location, $keyWord);

        $placeMessage = array();

        if (empty($placeData)) {

            $placeMessage[] = [
                'type' => 'text',
                'text' => "{$location['title']}の近くに{$keyWord}がありません"
            ];

        } else {

            $placeMessage[] = [
                'type' => 'text',
                'text' => "{$location['title']}の近くに{$keyWord}でサーチします"
            ];

            // The Line api get an error if the counts of message is more than 4
            $length = count($placeData) > 4 ? 4 : count($placeData);

            for ($i = 0; $i < $length; $i++) {

                $value = $placeData[$i];
                $place = [];
                $place['type'] = 'location';
                $place['title'] = "{$value['name']}";
                $place['address'] = $value['vicinity'];
                $place['latitude'] = $value['geometry']['location']['lat'];
                $place['longitude'] = $value['geometry']['location']['lng'];

                $placeMessage[] = $place;

            }
        }

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => $placeMessage
        ];

        $this->bot->replyMessage($message);

        $this->userData->modeTurnOff('place');
        $this->userData->deleteLocationData();

    }

    private function endPlaceMode()
    {

        $this->userData->modeTurnOff('place');

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '場所検索モードが終了しました'
                ]
            ]
        ];

        $this->bot->replyMessage($message);

        $this->userData->deleteLocationData();
    }

}