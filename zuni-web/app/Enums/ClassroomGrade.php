<?php

namespace App\Enums;

enum ClassroomGrade: string
{
    case FIRST_JARDIM = '1-JAD';
    case SECOND_JARDIM = '2-JAD';

    case FIRST_YEAR = '1-ANO';
    case SECOND_YEAR = '2-ANO';
    case THIRD_YEAR = '3-ANO';
    case FOURTH_YEAR = '4-ANO';
    case FIFTH_YEAR = '5-ANO';
    case SIXTH_YEAR = '6-ANO';
    case SEVENTH_YEAR = '7-ANO';
    case EIGHTH_YEAR = '8-ANO';
    case NINTH_YEAR = '9-ANO';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_JARDIM => '1º Jardim',
            self::SECOND_JARDIM => '2º Jardim',

            self::FIRST_YEAR => '1º Ano',
            self::SECOND_YEAR => '2º Ano',
            self::THIRD_YEAR => '3º Ano',
            self::FOURTH_YEAR => '4º Ano',
            self::FIFTH_YEAR => '5º Ano',
            self::SIXTH_YEAR => '6º Ano',
            self::SEVENTH_YEAR => '7º Ano',
            self::EIGHTH_YEAR => '8º Ano',
            self::NINTH_YEAR => '9º Ano',
        };
    }
}