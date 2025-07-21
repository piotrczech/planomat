<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Console;

use App\Application\Jobs\SendMissingDataNotificationJob;
use App\Application\UseCases\Notifications\GetUsersWithMissingSemesterConsultationsUseCase;
use App\Domain\Dto\MissingDataNotificationDto;
use Illuminate\Console\Command;

final class SendSemesterConsultationRemindersCommand extends Command
{
    protected $signature = 'consultation:mail:send-semester-reminders';

    protected $description = 'Sends reminders to users with missing semester consultations.';

    public function handle(GetUsersWithMissingSemesterConsultationsUseCase $useCase): int
    {
        $this->info('Checking for users with missing semester consultations...');

        $users = $useCase->execute();

        if (empty($users)) {
            $this->info('No users with missing semester consultations found.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d user(s) with missing semester consultations. Dispatching jobs...', count($users)));

        foreach ($users as $userData) {
            $notificationDto = new MissingDataNotificationDto(
                user: $userData['user'],
                semester: $userData['semester'],
                type: 'semester_consultations',
                daysOverdue: (int) $userData['days_overdue'],
            );

            SendMissingDataNotificationJob::dispatch($notificationDto);
        }

        $this->info('Reminder jobs dispatched successfully.');

        return self::SUCCESS;
    }
}
