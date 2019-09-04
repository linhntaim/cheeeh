<?php

namespace App\V1\Utils\Mail;

use App\V1\Utils\ClassTrait;
use App\V1\Utils\ClientAppHelper;
use App\V1\Utils\ConfigHelper;
use Illuminate\Mail\Mailable;

class TemplateNowMailable extends Mailable
{
    use ClassTrait;

    const EMAIL_FROM = 'x_email_from';
    const EMAIL_FROM_NAME = 'x_email_from_name';
    const EMAIL_SUBJECT = 'x_email_subject';
    const EMAIL_TO = 'x_email_to';
    const EMAIL_TO_NAME = 'x_email_to_name';

    protected $templateName;

    protected $templateParams;

    protected $useLocalizedTemplate;

    public function __construct($templateName, $templateParams = [], $useLocalizedTemplate = true, $locale = null)
    {
        $this->templateName = $templateName;

        $noReplyMail = ConfigHelper::getNoReplyMail();
        if (!isset($templateParams[static::EMAIL_FROM])) {
            $templateParams[static::EMAIL_FROM] = $noReplyMail['email'];
        }
        if (!isset($templateParams[static::EMAIL_FROM_NAME])) {
            $templateParams[static::EMAIL_FROM_NAME] = $noReplyMail['name'];
        }
        if (!isset($templateParams[static::EMAIL_SUBJECT])) {
            $templateParams[static::EMAIL_SUBJECT] = $this->__transWithModule('default_subject', 'label', [
                'app_name' => ClientAppHelper::getInstance()->getName()
            ]);
        }
        $this->templateParams = $templateParams;

        $this->useLocalizedTemplate = $useLocalizedTemplate;

        $this->locale(empty($locale) ? ConfigHelper::getCurrentLocale() : $locale);
    }

    public function getTemplatePath()
    {
        return 'emails.' . $this->templateName . ($this->useLocalizedTemplate ? '.' . $this->locale : '');
    }

    public function build()
    {
        $this->from($this->templateParams[static::EMAIL_FROM], $this->templateParams[static::EMAIL_FROM_NAME]);
        $this->subject($this->templateParams[static::EMAIL_SUBJECT]);
        if (isset($this->templateParams[static::EMAIL_TO_NAME])) {
            $this->to($this->templateParams[static::EMAIL_TO], $this->templateParams[static::EMAIL_TO_NAME]);
        } else {
            $this->to($this->templateParams[static::EMAIL_TO]);
        }
        return $this->view($this->getTemplatePath(), $this->templateParams);
    }
}
