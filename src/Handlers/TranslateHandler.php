<?php
/**
 * Handle translate request
 * PHP 7.2.7
 */

namespace Linebot\Handlers;

use Linebot\Apis\TranslateApi;


class TranslateHandler extends CoreHandler
{
    /**
     * Check the message is text or not, 
     */
    public function handle()
    {
        $text = $this->isTextMessage();

        if (! $text) {
            return $this->sendIndexMessage();
        }

        if ($text === "終了" || $text === '종료') {
            return $this->endTranslateMode();
        }

        return $this->sendTranslatedText($text);
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
                    'text' => "翻訳モードはテキストだけできます。
終了をご希望の場合、「終了」を入力してください"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    /**
     * Call Api and get translated message and send it to user
     * 
     * @return null
     */
    private function sendTranslatedText($text)
    {
        $translateApi = new TranslateApi($text);
        $translateText = $translateApi->getTranslatedText();

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $translateText
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    /**
     * Delete translate request on the database
     * 
     * @return null
     */
    private function endTranslateMode()
    {
        $this->userData->modeTurnOff('translate');

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '翻訳モードが終了しました'
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }
}