<?php

namespace App\V1\Notifications;

use App\V1\Configuration;
use App\V1\ModelRepositories\UserRepository;
use App\V1\Models\User;
use App\V1\Utils\ClassTrait;
use App\V1\Utils\ClientAppTrait;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Mail\TemplateMailable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

abstract class NowNotification extends BaseNotification
{
    use SerializesModels, ClassTrait, ClientAppTrait;

    const NAME = 'now_notification';

    protected static function __transNotification($name, $replace = [], $locale = null)
    {
        return static::__transWithSpecificModule($name, 'notification', $replace, $locale);
    }

    public $shouldStore;
    public $shouldWeb;
    public $shouldMail;
    public $shouldIos;
    public $shouldAndroid;

    /**
     * @var User
     */
    public $fromUser;

    public function __construct($fromUser = null)
    {
        $this->initClientApp();

        $this->shouldWeb = false;
        $this->shouldStore = false;
        $this->shouldMail = false;
        $this->shouldIos = false;
        $this->shouldAndroid = false;

        $this->setFromUser($fromUser);
    }

    public function setFromUser(User $user = null)
    {
        $this->fromUser = empty($user) ?
            (new UserRepository())->getById(Configuration::USER_SYSTEM_ID) : $user;

        return $this;
    }

    public function shouldWeb()
    {
        $this->shouldWeb = true;
        return $this;
    }

    public function shouldStore()
    {
        $this->shouldStore = true;
        return $this;
    }

    public function shouldIos()
    {
        $this->shouldIos = true;
        return $this;
    }

    public function shouldAndroid()
    {
        $this->shouldAndroid = true;
        return $this;
    }

    public function shouldMail()
    {
        $this->shouldMail = true;
        return $this;
    }

    protected function shouldSomething()
    {
        return $this->shouldMail || $this->shouldWeb || $this->shouldStore
            || $this->shouldIos || $this->shouldAndroid;
    }

    public function via($notifiable)
    {
        $via = [];
        if ($this->shouldStore) {
            $via[] = 'database';
        }
        if ($this->shouldWeb) {
            $via[] = 'broadcast';
        }
        if ($this->shouldMail) {
            $via[] = 'mail';
        }
        if ($this->shouldIos) {
            $via[] = 'ios';
        }
        if ($this->shouldAndroid) {
            $via[] = 'android';
        }
        return $via;
    }

    public function toBroadcast($notifiable)
    {
        $dateTimeHelper = DateTimeHelper::fromUser($notifiable);
        return (new BroadcastMessage([
            'id' => $this->id,
            'data' => $this->toArray($notifiable),
            'created_at' => DateTimeHelper::syncNow(),
            'shown_created_at' => $dateTimeHelper->compound('shortDate', ' ', 'shortTime'),
            'is_read' => false,
        ]));
    }

    public function toDatabase($notifiable)
    {
        return [
            'sender_id' => $this->fromUser->id,
        ];
    }

    public function toMail($notifiable)
    {
        return new TemplateMailable(
            $this->getMailTemplate($notifiable),
            array_merge([
                TemplateMailable::EMAIL_TO => $notifiable->preferredEmail(),
                TemplateMailable::EMAIL_TO_NAME => $notifiable->preferredName(),
                TemplateMailable::EMAIL_SUBJECT => $this->getMailSubject($notifiable),
            ], $this->getMailParams($notifiable)),
            $this->getMailUseLocalizedTemplate($notifiable),
            $this->locale
        );
    }

    public function toIos($notifiable)
    {
        return [
            'aps' => [
                'alert' => [
                    'title' => $this->getTitle($notifiable),
                    'body' => $this->getContent($notifiable, false),
                ],
                'sound' => 'default',
                'badge' => 1,
            ],
            'extraPayLoad' => [
                'action' => $this->getAction($notifiable),
            ],
        ];
    }

    public function toAndroid($notifiable)
    {
        return [
            'notification' => [
                'title' => $this->getTitle($notifiable),
                'body' => $this->getContent($notifiable, false),
                'sound' => 'default',
            ],
            'data' => [
                'action' => $this->getAction($notifiable),
            ],
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'name' => $this::NAME,
            'image' => $this->fromUser->url_avatar,
            'content' => $this->getContent($notifiable, false),
            'html_content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
        ];
    }

    protected function getTitle($notifiable)
    {
        return null;
    }

    protected function getContent($notifiable, $html = true)
    {
        return null;
    }

    protected function getAction($notifiable)
    {
        return null;
    }

    protected function getMailTemplate($notifiable)
    {
        return null;
    }

    protected function getMailSubject($notifiable)
    {
        return null;
    }

    protected function getMailParams($notifiable)
    {
        return [];
    }

    protected function getMailUseLocalizedTemplate($notifiable)
    {
        return true;
    }

    protected function getNotifiables()
    {
        return null;
    }

    public function cannotSend($notifiables)
    {
        return empty($notifiables)
            || ($notifiables instanceof Collection && $notifiables->count() <= 0)
            || !$this->shouldSomething();
    }

    public function send()
    {
        $notifiables = $this->getNotifiables();

        if ($this->cannotSend($notifiables)) return false;

        Notification::send($notifiables, $this);

        return true;
    }
}
