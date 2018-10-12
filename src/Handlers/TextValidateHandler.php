<?php
/**
 * Text validate handler
 * 
 * PHP 7.2.7
 */

namespace Linebot\Handlers;

/**
 * Figure out what the user wants using reqular expression.
 */
class TextValidateHandler extends CoreHandler
{
    /**
     * Using regular expression, figure out what user's request is and call
     * method that insert the request database and 
     * send message to user to ask addtional information
     * 
     * @return method
     */
    public function handle()
    {
        // Check the message is text
        $text = $this->isTextMessage();

        // if it is not text, send notification to user
        if (! $text) {
            return $this->sendIndexMessage();
        }

        // Check the request 
        if (preg_match('/翻訳|ほにゃく|번역/', $text)) {
            return $this->startTranslateMode();
        }

        if (preg_match('/場所検索|주변검색/', $text)) {
            return $this->startPlaceMode();
        }

        if (preg_match('/天気|てんき|날씨/', $text)) {
            return $this->startWeatherMode();
        }

        preg_match('/((?P<won>\d+)ウォン|(?P<yen>\d+)円|(?P<rate>為替))/', $text, $matches);

        if (! empty($matches)) {
            return $this->startCurrencyMode($matches);
        }

        return $this->sendIndexMessage();
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
     * Send the notification and welcome message to user
     * 
     * @return null
     */
    private function sendIndexMessage()
    {
        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "私はあなたの韓国旅行ガイドです！
私は翻訳、為替、天気、場所検索ができます。
翻訳をしたい'翻訳'と入力すると,
為替をしたい'為替'と入力すると,
天気をしたい'天気'と入力すると,
場所検索をしたい'場所検索'と入力してください！"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }


    /**
     * Call CurrencyHandler
     * 
     * @return null
     */
    private function startCurrencyMode($matches)
    {
        $currencyHandler = new CurrencyHandler($this->bot, $this->userData, $this->event);

        $currencyHandler->handle($matches);
    }

    /**
     * Insert user's requst to database and
     * send message that you can use transltor
     */
    private function startTranslateMode()
    {
        $this->userData->modeTurnOn('translate');

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "翻訳モードが開始されました終了をご希望の「終了」を入力してください"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    /**
     * Insert user's requst to database and
     * send message that askes user locationdata to user
     */
    protected function startPlaceMode()
    {
        $this->userData->modeTurnOn('place');

        $this->sendLocationMessage();
    }


    /**
     * Insert user's requst to database and
     * send message that askes user locationdata to user
     */
    protected function startWeatherMode()
    {
        $this->userData->modeTurnOn('weather');

        $this->sendLocationMessage();

    }

    /**
     * Send message that asks user's location data to user
     * 
     * @return null
     */
    protected function sendLocationMessage() {
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
}