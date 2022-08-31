<?php

namespace Bot\App;

class JsonFileData
{
    private $data;
    private $count_question;

    public function __construct()
    {
        $this->data = json_decode(file_get_contents('assets/json/data.json'),true);
        $this->count_question = count($this->data);
    }

    /**
     * Получем текст вопроса опроса
     * @param int $number_question
     * @return string
     */
    public function getQuestion(int $number_question): string
    {
        return $this->data[$number_question]['question'];
    }

    /**
     * Получаем варианты ответов на вопрос
     * @param int $number_question
     * @return array
     */
    public function getAnswerOptions(int $number_question): array
    {
        return $this->data[$number_question]['answer_options'];
    }

    /**
     * Сравниваем ответ пользователя с правильным ответом на вопрос
     * @param int $number_question
     * @param string $answer
     * @return int
     */
    public function checkResponse(int $number_question,string $answer): int
    {
        $correct_answer = $this->data[$number_question]['correct_answer'];

        return $correct_answer == $answer ? 1 : 0;
    }

    /**
     * @return int
     */
    public function getCountQuestion(): int
    {
        return $this->count_question;
    }



}