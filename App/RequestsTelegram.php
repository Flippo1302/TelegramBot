<?php

namespace Bot\App;

class RequestsTelegram
{

    public function __construct(
        private $data,
        private $bot_token,
        private $api_url = "https://api.telegram.org/bot"
    )
    {
    }

    /** Получаем массив данных от запроса телеграм
     * @return array
     */
    public function getData(): array
    {
        return json_decode($this->data, true);
    }

    /** Отправляем запрос в телеграм
     * @param array $data
     * @param string $type
     * @return void
     */
    public function requestToTelegram(array $data, string $type): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url . $this->bot_token . '/' . $type);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);

    }
}