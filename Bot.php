<?php

namespace Bot;

require_once('autoload.php');

use Bot\App\JsonFileData;
use Bot\App\User;
use Bot\App\Button;
use Bot\App\RequestsTelegram;
use Bot\App\Config;

$bot = new Bot();
$bot->activateBot();

class Bot
{

    private $requests_telegram;
    private $button;
    private $user;
    private $data_file;

    public function __construct()
    {
        $config = new Config();
        if (!empty(file_get_contents('php://input'))) {
            $this->requests_telegram = new RequestsTelegram(file_get_contents('php://input'), $config->getBotToken());
            $this->button = new Button();
            $this->data_file = new JsonFileData();
            $this->user = new User($this->requests_telegram->getData());
        }

    }

    /**
     * Активация бота и обработка данных от пользователя
     * @return bool
     */
    public function activateBot(): bool
    {
        if (!is_object($this->user)) {
            return false;
        }

        /**
         * Если приходит сообщение от пользователя
         */
        if (array_key_exists('message', $this->requests_telegram->getData())) {
            $this->messageProcessing();
        }

        /**
         * Если приходит нажатие кнопки от пользователя
         */
        if (array_key_exists('callback_query', $this->requests_telegram->getData())) {
            $this->deleteMessage($this->user->getChatId(), $this->user->getMessageId());
            $this->buttonProcessing();
        }

        return true;

    }

    /**
     * Выполняем действия в зависимости от нажатой кнопки пользователем
     * @return void
     */
    private function buttonProcessing(): void
    {
        if (stripos($this->user->getCallbackData(), 'cancel') !== false) {
            $this->sendMessage($this->user->getChatId(), "Привет {$this->user->getFirstName()} {$this->user->getLastName()}, сможешь пройти тест?", $this->button->getButton('start'));

        }

        if ($this->user->getCallbackData() == 'start_test') {
            $this->sendDataQuestion(0);

        }

        if (is_numeric($this->user->getCallbackData())) {
            $inlineKeyboard = $this->user->getInlineKeyboard();
            $end_inline_keyboard = end($inlineKeyboard);
            $number_question = substr_count($end_inline_keyboard[0]['callback_data'], '|');

            $check_response = $this->data_file->checkResponse($number_question, $this->user->getCallbackData());
            $response_user = str_replace('cancel', '', $end_inline_keyboard[0]['callback_data']);

            if ($this->data_file->getCountQuestion() == $number_question + 1) {
                $count_answer_question = substr_count($response_user . $check_response, '1');
                $this->sendMessage($this->user->getChatId(), "Правильных ответов $count_answer_question из {$this->data_file->getCountQuestion()}, хочешь попробовать ещё?", $this->button->getButton('start'));
            } else {
                $this->sendDataQuestion($number_question + 1, $response_user . "|" . $check_response);
            }
        }
    }

    /**
     * Выполняем действия в зависимости от текста сообщения пользователя
     * @return void
     */
    private function messageProcessing(): void
    {
        if ($this->user->getMessage() == "/start") {
            $this->sendMessage($this->user->getChatId(), "Привет {$this->user->getFirstName()} {$this->user->getLastName()}, сможешь пройти тест?", $this->button->getButton('start'));

        }
    }

    /**
     * Отправка вопроса и варианты ответов пользователю
     * @param int $number_question
     * @param string $response_user
     * @return void
     */
    private function sendDataQuestion(int $number_question, string $response_user = ''): void
    {
        $question = $this->data_file->getQuestion($number_question);
        $answer_options = $this->data_file->getAnswerOptions($number_question);

        $this->sendMessage($this->user->getChatId(), "Вопрос: $question", $this->button->getAnswerOptionsButtons($answer_options, $response_user));
    }

    /**
     * Удаление сообщения
     * @param int $chatId
     * @param int $messageID
     * @return void
     */
    private function deleteMessage(int $chatId, int $messageID): void
    {
        $this->requests_telegram->requestToTelegram([
            'chat_id' => $chatId,
            'message_id' => $messageID,
        ], "deleteMessage");
    }

    /**
     * Отправка сообщения
     * @param int $chat_id
     * @param string $text
     * @param string|false $buttons
     * @return void
     */
    private function sendMessage(int $chat_id, string $text, string|false $buttons = false): void
    {
        $this->requests_telegram->requestToTelegram([
            'chat_id' => $chat_id,
            'text' => $text,
            "parse_mode" => "HTML",
            'reply_markup' => $buttons
        ], "sendMessage");
    }

}