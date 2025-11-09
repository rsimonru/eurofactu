<?php
namespace App\Classes;

use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class emtSign {

    /**
     * Create a sign for the value.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function sign(string $value, string $key = null)
    {
        if (!$key) {
            $key = session()->getId();
        }
        $signature = (string) hash_hmac('sha256', $value, $key);

        return $signature;
    }

    /**
     * Determine if the given value has a valid signature.
     *
     * @param  string  $value
     * @param  string  $valueSignature
     * @return bool
     */
    public static function hasValidSignature(string $value, string $valueSignature, string $key = null)
    {   if (!$key) {
            $key = session()->getId();
        }
        $signature = hash_hmac('sha256', $value, $key);

        return hash_equals($signature, $valueSignature);
    }

}
