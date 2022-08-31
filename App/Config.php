<?php

namespace Bot\App;

class Config
{
 public $bot_token = "5558104164:AAEZtrnc7bhgIoRgHsCoPONfetbIB004UMU";

    /**
     * @return string
     */
    public function getBotToken(): string
    {
        return $this->bot_token;
    }
}