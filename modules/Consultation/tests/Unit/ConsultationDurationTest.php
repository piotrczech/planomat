<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;
use Carbon\Carbon;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Tests\Unit\Data\ConsultationDataProvider;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class ConsultationDurationTest extends TestCase
{
    protected $mockRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepo = Mockery::mock(ConsultationRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    #[DataProviderExternal(ConsultationDataProvider::class, 'consultationDurationDataProvider')]
    public function it_validates_consultation_duration_correctly($startTime, $endTime, $isValid): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => WeekTypeEnum::ALL->value,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => $startTime,
            'consultationEndTime' => $endTime,
            'consultationLocation' => 'Room 101',
        ]);

        $durationInMinutes = $this->calculateDurationInMinutes($startTime, $endTime);
        $isValidDuration = $this->isValidConsultationDuration($durationInMinutes);

        $this->assertEquals($isValid, $isValidDuration);
    }

    private function calculateDurationInMinutes(string $startTime, string $endTime): int
    {
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        return abs($end->diffInMinutes($start));
    }

    private function isValidConsultationDuration(int $durationInMinutes): bool
    {
        return $durationInMinutes >= 60 && $durationInMinutes <= 180;
    }
}
