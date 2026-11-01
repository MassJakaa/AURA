<?php

namespace App\Enums;

enum ActivityCategory: string
{
    case DAILY_ROUTINE = 'daily_routine';
    case HOBBY = 'hobby';
    case RELAXATION = 'relaxation';
    case EXPLORATION = 'exploration';
    case SPECIAL_EVENT = 'special_event';
    case SPONTANEOUS = 'spontaneous';

    public function label(): string
    {
        return match($this) {
            self::DAILY_ROUTINE => 'Daily Routine',
            self::HOBBY => 'Hobby',
            self::RELAXATION => 'Relaxation',
            self::EXPLORATION => 'Exploration',
            self::SPECIAL_EVENT => 'Special Event',
            self::SPONTANEOUS => 'Spontaneous',
        };
    }
}
