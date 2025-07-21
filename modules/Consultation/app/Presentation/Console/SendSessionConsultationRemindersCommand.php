<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Console;

use App\Application\Jobs\SendMissingDataNotificationJob;
use App\Application\UseCases\Notifications\GetUsersWithMissingSessionConsultationsUseCase;
use App\Domain\Dto\MissingDataNotificationDto;
use Illuminate\Console\Command;

final class SendSessionConsultationRemindersCommand extends Command
{
    protected $signature = 'consultation:mail:send-session-reminders';

    protected $description = 'Sends reminders to users with missing session consultations.';

    public function handle(GetUsersWithMissingSessionConsultationsUseCase $useCase): int
    {
        $this->info('Checking for users with missing session consultations...');

        $users = $useCase->execute();

        if (empty($users)) {
            $this->info('No users with missing session consultations found.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d user(s) with missing session consultations. Dispatching jobs...', count($users)));

        foreach ($users as $userData) {
            $notificationDto = new MissingDataNotificationDto(
                user: $userData['user'],
                semester: $userData['semester'],
                type: 'session_consultations',
                daysOverdue: (int) $userData['days_overdue'],
            );

            SendMissingDataNotificationJob::dispatch($notificationDto);
        }

        $this->info('Reminder jobs dispatched successfully.');

        return self::SUCCESS;
    }
}
