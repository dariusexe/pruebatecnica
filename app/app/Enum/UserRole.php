<?php
namespace App\Enum;


enum UserRole:int{
    case PARTICIPANT = 1;
    case ADMIN = 2;
}