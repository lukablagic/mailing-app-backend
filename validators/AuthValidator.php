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
    // validate regitsre
    public static function validateRegister($payload)
    {
        $response = [];
        if (!isset($payload['name'])) {
            $response[] = 'name';
        }
        if (!isset($payload['surname'])) {
            $response[] = 'surname';
        }
        if (!isset($payload['email'])) {
            $response[] = 'email';
        }
        if (!isset($payload['password'])) {
            $response[] = 'password';
        }

        if (!empty($response)) {
            RequestHandler::unprocessableEntity($response);
        }
    }
}
