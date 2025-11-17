<?php

class StringHelper
{
    private const BOOK_CONDITION_LABELS = [
        "comme_neuf" => "Comme neuf",
        "tres_bon" => "Très bon",
        "bon" => "Bon",
        "correct" => "Correct",
        "abime" => "Abîmé",
    ];

    public static function bookConditionLabel(?string $condition): string
    {
        return self::BOOK_CONDITION_LABELS[$condition] ?? ucfirst((string)$condition);
    }
}

