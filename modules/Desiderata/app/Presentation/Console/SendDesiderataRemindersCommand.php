<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Console;

use App\Application\Jobs\SendMissingDataNotificationJob;
use App\Application\UseCases\Notifications\GetUsersWithMissingDesiderataUseCase;
use App\Domain\Dto\MissingDataNotificationDto;
use Illuminate\Console\Command;

final class SendDesiderataRemindersCommand extends Command
{
    protected $signature = 'desiderata:mail:send-reminders';

    protected $description = 'Sends reminders to users with missing desiderata.';

    public function handle(GetUsersWithMissingDesiderataUseCase $useCase): int
    {
        $this->info('Checking for users with missing desiderata...');

        $users = $useCase->execute();

        if (empty($users)) {
            $this->info('No users with missing desiderata found.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d user(s) with missing desiderata. Dispatching jobs...', count($users)));

        foreach ($users as $userData) {
            $notificationDto = new MissingDataNotificationDto(
                user: $userData['user'],
                semester: $userData['semester'],
                type: 'desiderata',
                daysOverdue: (int) $userData['days_overdue'],
            );

            SendMissingDataNotificationJob::dispatch($notificationDto);
        }

        $this->info('Reminder jobs dispatched successfully.');

        return self::SUCCESS;
    }
}
