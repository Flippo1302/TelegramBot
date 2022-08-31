<?php

namespace Bot\App;

class User
{
    private $chat_id;
    private $message;
    private $message_id;
    private $first_name;
    private $last_name;
    private $username;
    private $callback_data;
    private $inline_keyboard;

    public function __construct($data)
    {
        if (array_key_exists('message', $data)) {
            $this->chat_id = $data['message']['chat']['id'];
            $this->first_name = $data['message']['chat']['first_name'];
            $this->last_name = $data['message']['chat']['last_name'];
            $this->username = $data['message']['chat']['username'];
            $this->message = $data['message']['text'];
        }
        if (array_key_exists('callback_query', $data)) {
            $this->chat_id = $data['callback_query']['message']['chat']['id'];
            $this->first_name = $data['callback_query']['message']['chat']['first_name'];
            $this->last_name = $data['callback_query']['message']['chat']['last_name'];
            $this->username = $data['callback_query']['message']['chat']['username'];
            $this->callback_data = $data['callback_query']['data'];
            $this->message_id = $data['callback_query']['message']['message_id'];
            $this->inline_keyboard = $data['callback_query']['message']['reply_markup']['inline_keyboard'];
        }
    }

    public function getInlineKeyboard(): array
    {
        return $this->inline_keyboard;
    }

    public function getMessageId(): int
    {
        return $this->message_id;
    }

    public function getCallbackData(): string
    {
        return $this->callback_data;
    }

    public function getChatId(): int
    {
        return $this->chat_id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}