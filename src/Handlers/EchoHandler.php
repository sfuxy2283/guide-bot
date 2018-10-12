<?php
/**
 * Created by PhpStorm.
 * User: sangho
 * Date: 2018-08-20
 * Time: 오후 11:17
 */

namespace Linebot\Handlers;


class EchoHandler extends CoreHandler
{
    public function handle()
    {
        $text = $this->isTextMessage();

        if (! $text) {

            return $this->sendIndexMessage();

        }


        if ($text === "종료") {
            return $this->endEchoMode();
        }


        return $this->sendEchoMessage($text);

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

    private function sendIndexMessage()
    {
        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "따라하기 모드는 텍스트만 지원가능합니다 종료를 원하시면 '종료'를 입력해주세요"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    private function sendEchoMessage($echo)
    {
        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $echo
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    private function endEchoMode()
    {
        $this->userData->modeTurnOff('echo');

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '따라하기모드가 종료되었습니다'
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

}