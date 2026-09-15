<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WorqsyEventNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $event,
        public string $title,
        public string $body,
        public ?string $resourceType = null,
        public ?string $resourceId = null,
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        return [
            'event'=>$this->event,
            'title'=>$this->title,
            'body'=>$this->body,
            'resource_type'=>$this->resourceType,
            'resource_id'=>$this->resourceId,
            'url'=>$this->url,
        ];
    }
}
