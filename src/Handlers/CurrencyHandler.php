<?php
/**
 * Handler for request about currency
 * 
 * PHP 7.2.7
 */

namespace Linebot\Handlers;

use Linebot\Apis\CurrencyApi;

/**
 * Call currency api and fetch currency data and send it to user
 */
class CurrencyHandler extends CoreHandler
{
    public function handle($matches)
    {
        if ($matches['rate']) {
            $this->exchangeRate();
        }

        if ($matches['won']) {
            $this->wonToYen($matches['won']);
        }

        if ($matches['yen']) {
            $this->yenToWon($matches['yen']);
        }
    }

    private function exchangeRate()
    {
        $exchangeRate = CurrencyApi::getCurrencyData();

        $exchangeRate *= 100;
        $roundedRate = round($exchangeRate, 2);

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "今の為替レートは100円あたり{$roundedRate}ウォンです
「〜円」を入力すると、ウォンに「〜ウォン」を入力すると、円に変えています"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    private function yenToWon($yen)
    {
        $exchangeRate = CurrencyApi::getCurrencyData();

        $won = $exchangeRate * $yen;
        $roundedRate = round($won,2);

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "{$yen}円は{$roundedRate}ウォンです"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

    private function wonToYen($won)
    {
        $exchangeRate = CurrencyApi::getCurrencyData();

        $yen = 1 / $exchangeRate * $won;
        $roundedRate = round($yen,2);

        $message = [
            'replyToken' => $this->replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => "{$won}ウォンは{$roundedRate}円です"
                ]
            ]
        ];

        $this->bot->replyMessage($message);
    }

}