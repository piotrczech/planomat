<?php

declare(strict_types=1);

namespace App\Application\Mail;

use App\Domain\Dto\MissingDataNotificationDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class MissingDataNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly MissingDataNotificationDto $notificationDto,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('notifications.missing_data_subject'))
            ->view('emails.missing-data-notification')
            ->with([
                'notificationDto' => $this->notificationDto,
            ]);
    }
}
