<?php

namespace Linebot\Core;

use Linebot\Utils\Auth;

class LineApi
{
    /**
     * Line Channel Secret
     * 
     * @var string
     */
    private $lineChannelSecret;

    /**
     * Line channel token
     * 
     * @var string
     */
    private $lineChannelToken;

    /**
     * Constructor of class
     * 
     * @return void
     */
    public function __construct()
    {
        $this->lineChannelSecret = getenv("LINE_CHANNEL_SECRET");
        $this->lineChannelToken = getenv("LINE_CHANNEL_TOKEN");
    }

    /**
     * Parse event from the user
     *
     * @return array The array has event data form user
     * @throws 
     */
    public function parseEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            throw new \Exception("Method not allowed");
        }

        $entityBody = file_get_contents('php://input');

        if (strlen($entityBody) === 0) {

            http_response_code(400);

            throw new \Exception("Missing request body");
        }

        if (! Auth::hash_equals(Auth::sign($entityBody, $this->lineChannelSecret), $_SERVER['HTTP_X_LINE_SIGNATURE'])) {

            http_response_code(400);

            throw new \Exception("Invalid signature value");
        }

        $data = json_decode($entityBody, true);

        if (!isset($data['events'])) {

            http_response_code(400);

            throw new \Exception("Invalid request body: missing events property");

        }

        $event = $data['events'][0];

        return $event;
    }

    /**
     * Reply message to the user
     *
     * @param $message The message to reply
     * @param $accessToken The token to access the channel
     *
     * @return void
     */
    public function replyMessage($message)
    {
        $header = array(
            "Content-Type: application/json",
            'Authorization: Bearer ' . $this->lineChannelToken,
        );

        $context = stream_context_create(array(
            "http" => array(
                "method" => "POST",
                "header" => implode("\r\n", $header),
                "content" => json_encode($message),
            ),
        ));

        $response = file_get_contents('https://api.line.me/v2/bot/message/reply', false, $context);

        if (strpos($http_response_header[0], '200') === false) {
            http_response_code(500);

            throw new \Exception("Request failed: " . $response);
        }
    }
}