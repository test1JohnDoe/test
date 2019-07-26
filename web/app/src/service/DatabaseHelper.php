<?php

namespace App\Acme\service;

trait DatabaseHelper
{
    /**
     * @param array $parameters
     * @return string
     */
    public function buildQuestionMarks(array $parameters): string
    {
        return implode(', ', array_fill(0, count($parameters), '?'));
    }
}