<?php

namespace Validator;

use Utility\RequestHandler;

class AuthValidator
{


    public static function validateLogin($payload)
    {
        if (!isset($payload['email']) || !isset($payload['password'])) {
            RequestHandler::unprocessableEntity('email');
        }
        if (empty($payload['email']) || empty($payload['password'])) {
            RequestHandler::unprocessableEntity('password');
        }
    }
}