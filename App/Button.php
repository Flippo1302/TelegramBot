<?php

namespace Bot\App;

class Button
{
    /**
     * Формируем набор кнопок в зависимости от параметра
     * @param string $name_button
     * @return string
     */
    public function getButton(string $name_button): string
    {
        switch ($name_button) {
            case 'start':
                $button = $this->getInlineKeyBoard([
                    [
                        ["text" => "Пройти тест", "callback_data" => "start_test"]
                    ]
                ]);
                break;
        }

        return $button;

    }

    /**
     * Формируем кнопки из вариантов ответа на вопрос
     * @param array $data
     * @param string $response
     * @return string
     */
    public function getAnswerOptionsButtons(array $data,string $response): string
    {
        $buttons = [];
        for($i = 0; $i < count($data); $i++) {
            $buttons[][] = [
                'text' => $data[$i],
                'callback_data' => $data[$i]
            ];
        }
        $buttons[][] = [
            'text' => 'Отмена',
            'callback_data' => "cancel$response"
        ];


        return $this->getInlineKeyBoard($buttons);
    }

    /**
     * Формирования json для inline-кнопок телеграм
     * @param array $data
     * @return string
     */
    private function getInlineKeyBoard(array $data): string
    {
        $keyboard = array(
            "inline_keyboard" => $data,
            "one_time_keyboard" => false,
            "resize_keyboard" => true
        );
        return json_encode($keyboard);
    }



}