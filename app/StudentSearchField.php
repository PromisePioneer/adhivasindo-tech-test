<?php

namespace App;

enum StudentSearchField: string
{
    case Nama = 'nama';
    case Nim = 'nim';
    case Ymd = 'ymd';

    public function label(): string
    {
        return match ($this) {
            self::Nama => 'Name',
            self::Nim => 'NIM',
            self::Ymd => 'YMD',
        };
    }
}
