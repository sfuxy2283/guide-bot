<?php
/**
 * Router
 * 
 * PHP 7.2.7
 */

namespace Linebot\Core;

use Linebot\Handlers\PlaceHandler;
use Linebot\Handlers\TextValidateHandler;
use Linebot\Handlers\EchoHandler;
use Linebot\Handlers\TranslateHandler;
use Linebot\Handlers\WeatherHandler;

class Router
{
    /**
     * The Line bot
     * 
     * @var LineApi class
     */
    private $bot;
    
    /**
     * The user's data on the database
     * 
     * @var UserData
     */
    private $userData;
    
    /**
     * The message event from the user
     * 
     * @var array
     */
    private $event;


    /**
     * Class constructor
     * 
     * @param DependencyInjector
     * @return null
     */
    public function __construct($di)
    {
        $this->bot = $di->get('bot');
        $this->userData = $di->get('userData');
        $this->event = $di->get('event');

        $handler = $this->callHandler();
        $handler->handle();
    }

    /**
     * Check what is prvious user's requst and send it to
     * handler which can handle it, if there isn't user's previous request send
     * it to the text validate handler
     *  
     * @return Handler
     */
    public function callHandler()
    {
        if ($this->userData->isTranslateMode()) {

            return new TranslateHandler($this->bot, $this->userData, $this->event);
        }

        if ($this->userData->isPlaceMode()) {

            return new PlaceHandler($this->bot, $this->userData, $this->event);
        }

        if ($this->userData->isWeatherMode()) {

            return new WeatherHandler($this->bot, $this->userData, $this->event);
        }

        return new TextValidateHandler($this->bot, $this->userData, $this->event);

    }


}