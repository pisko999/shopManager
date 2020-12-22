<?php


namespace App\services;


class messagerieService
{
    public static function errorMessage($message){
        session()->push('messages', ['type' => 'error', 'message' => $message]);

    }
    public static function successMessage($message){
        session()->push('messages', ['type' => 'success', 'message' => $message]);

    }

}
