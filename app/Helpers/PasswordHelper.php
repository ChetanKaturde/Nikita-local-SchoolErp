<?php

namespace App\Helpers;

class PasswordHelper
{
    /**
     * Generate a random password
     * 
     * @param int $length Password length
     * @return string
     */
    public static function generate($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }

    /**
     * Generate password in format: First3LettersOfName + 4RandomDigits
     * Example: RAH1234
     * 
     * @param string $firstName
     * @return string
     */
    public static function generateFormatted($firstName)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $firstName), 0, 3));
        $suffix = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $suffix;
    }
}
