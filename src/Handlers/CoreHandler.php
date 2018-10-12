<?php

namespace Linebot\Handlers;

abstract class CoreHandler
{
    protected $bot;
    protected $userData;
    protected $event;
    protected $replyToken;

    /**
     * Class constructor
     * 
     * @param $bot Line chat bot
     * @param $userData User data in the database
     * @param $event Event from the user
     */
    public function __construct($bot, $userData, $event)
    {
        $this->bot = $bot;
        $this->userData = $userData;
        $this->event = $event;
        $this->replyToken = $event['replyToken'];
    }
}