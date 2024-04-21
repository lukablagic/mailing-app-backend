<?php

namespace Validator;

use Utility\RequestHandler;

class MailValidator
{
    public static function validateSendingDraft($payload)
    {
        if (!isset($payload['draft'])) {
            RequestHandler::unprocessableEntity('draft');
        }
        $draft = $payload['draft'];
        if (!isset($draft['to']) || !isset($draft['subject']) || !isset($draft['body'])) {
            RequestHandler::unprocessableEntity('to, subject, body');
        }
        if (!is_array($draft['to'])) {
            RequestHandler::unprocessableEntity('to');
        }
        if (!is_string($draft['subject'])) {
            RequestHandler::unprocessableEntity('subject');
        }
        if (!is_string($draft['body'])) {
            RequestHandler::unprocessableEntity('body');
        }
        if (isset($draft['cc']) && !is_array($draft['cc'])) {
            RequestHandler::unprocessableEntity('cc');
        }
        if (isset($draft['bcc']) && !is_array($draft['bcc'])) {
            RequestHandler::unprocessableEntity('bcc');
        }

        return $draft;
    }
  
}
