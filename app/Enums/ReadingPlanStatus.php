<?php

namespace App\Enums;

enum ReadingPlanStatus:string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case OVERDUE = 'overdue';
}