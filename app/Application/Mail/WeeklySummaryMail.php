<?php

declare(strict_types=1);

namespace App\Application\Mail;

use App\Domain\Dto\WeeklySummaryDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class WeeklySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly WeeklySummaryDto $summaryDto,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('notifications.weekly_summary_subject'))
            ->view('emails.weekly-summary')
            ->with([
                'summaryDto' => $this->summaryDto,
            ]);
    }
}
