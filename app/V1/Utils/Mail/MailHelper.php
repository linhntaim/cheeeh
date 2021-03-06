<?php

namespace App\V1\Utils\Mail;

use App\V1\Exceptions\AppException;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class MailHelper
{
    public static function send(Mailable $mailable)
    {
        try {
            if ($mailable instanceof ShouldQueue) {
                Mail::queue($mailable);
            } else {
                Mail::send($mailable);
            }
            return true;
        } catch (Exception $ex) {
            throw AppException::from($ex);
        }
    }

    public static function sendWithTemplate($templatePath, $templateParams, $useLocalizedTemplate = true, $locale = null)
    {
        return static::send(new TemplateMailable($templatePath, $templateParams, $useLocalizedTemplate, $locale));
    }

    public static function sendNowWithTemplate($templatePath, $templateParams, $useLocalizedTemplate = true, $locale = null)
    {
        return static::send(new TemplateNowMailable($templatePath, $templateParams, $useLocalizedTemplate, $locale));
    }
}
