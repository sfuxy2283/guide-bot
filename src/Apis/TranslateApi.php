<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018-08-25
 * Time: 오후 10:47
 */

namespace Linebot\Apis;


class TranslateApi
{
    protected $clientID;
    protected $clientSecret;
    protected $text;
    protected $requestURL = 'https://openapi.naver.com/v1/language/translate';

    /**
     * TranslateApi constructor.
     *
     * @param $text The text message from the user
     */
    public function __construct($text)
    {
        $this->clientID = getenv('PAPAGO_CLIENT_ID');
        $this->clientSecret = getenv('PAPAGO_CLIENT_SECRET');

        $this->text = trim($text);
    }

    /**
     * Send post request to api and get translated text by response
     *
     * @return string The translated Text
     * @throws \Exception
     */
    public function getTranslatedText()
    {
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = "X-Naver-Client-Id: $this->clientID";
        $headers[] = "X-Naver-Client-Secret: $this->clientSecret";

        $postData = array(
            'source' => 'ja',
            'target' => 'ko',
            'text' => $this->text
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->requestURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception($error);
        }

        $responseData = json_decode($response, true);

        $translatedText = $responseData['message']['result']['translatedText'];

        return $translatedText;
    }



}