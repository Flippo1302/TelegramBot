<?php
//ini_set('log_errors', 'On');
//ini_set('error_log', 'log.txt');

require_once 'config.php';
// Создаем объект бота
$bot = new Bot($bot_token);
// Обрабатываем пришедшие данные
$bot->init('php://input');

/**
 * Class Bot
 */
class Bot
{

    private $bot_token;

    public function __construct($bot_token)
    {
        $this->bot_token = $bot_token;
    }

//    private $botToken = "5558104164:AAEZtrnc7bhgIoRgHsCoPONfetbIB004UMU";

    private $api_url = "https://api.telegram.org/bot";

    public function init($data_php)
    {
        // создаем массив из пришедших данных от API Telegram
        $data = $this->getData($data_php);
        // id чата отправителя
        $chat_id = $data['message']['chat']['id'];
        //включаем логирование будет лежать рядом с этим файлом
//        $this->setFileLog($data, "log.txt");
        // Кнопка отмены
        $otmena = $this->getKeyBoard([
            [
                ["text" => "Отмена"]
            ]
        ]);

        if (array_key_exists('message', $data)) {
            // пришла команда /start
            if ($data['message']['text'] == "/start") {
                $this->sendMessage($chat_id, 'test');

            }
        }

    }

    //клавиатура
    private function getKeyBoard($data)
    {
        $keyboard = array(
            "keyboard" => $data,
            "one_time_keyboard" => false,
            "resize_keyboard" => true
        );
        return json_encode($keyboard);
    }

    // функция отправки текстового сообщения
    private function sendMessage($chat_id, $text, $buttons = false)
    {
        $this->requestToTelegram([
            'chat_id' => $chat_id,
            'text' => $text,
            "parse_mode" => "HTML",
            'reply_markup' => $buttons
        ], "sendMessage");
    }

    // функция логирования в файл
    private function setFileLog($data, $file)
    {
        $fh = fopen($file, 'a') or die('can\'t open file');
        ((is_array($data)) || (is_object($data))) ? fwrite($fh, print_r($data, TRUE) . "\n") : fwrite($fh, $data . "\n");
        fclose($fh);
    }

    /**
     * Парсим что приходит преобразуем в массив
     * @param $data
     * @return mixed
     */
    private function getData($data)
    {
        return json_decode(file_get_contents($data), TRUE);
    }

    /** Отправляем запрос в Телеграмм
     * @param $data
     * @param string $type
     * @return mixed
     */
    private function requestToTelegram($data, $type)
    {
        $result = null;

        if (is_array($data)) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->api_url . $this->bot_token . '/' . $type);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $result;

    }

}