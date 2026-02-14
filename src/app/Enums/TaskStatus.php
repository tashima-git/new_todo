<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Stocked = 'stocked';
    case Killed = 'killed';
}
