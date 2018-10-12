<?php

/**
 * Parse user's message, fetche user's data from database and
 * send them to Router
 * 
 * PHP 7.2.7
 */
use Linebot\Core\LineApi;
use Linebot\Core\Router;
use Linebot\Core\UserData;

use Linebot\Utils\DependencyInjector;

require dirname(__DIR__) . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bot = new LineApi();

    $event = $bot->parseEvent();

    $userId = $event['source']['userId'];

    // Using user's Id, fetches data from the database
    $userData = new UserData(($userId));

    // Insert user's data, user's message, and chat bot to dependency injector 
    $di = new DependencyInjector();
    $di->set('bot', $bot);
    $di->set('userData', $userData);
    $di->set('event', $event);

    // Send dependency injector to router
    $router = new Router($di);
}





