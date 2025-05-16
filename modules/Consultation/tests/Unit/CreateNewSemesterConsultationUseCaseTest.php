<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;
use Modules\Consultation\Application\UseCases\ScientificWorker\CreateNewSemesterConsultationUseCase;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Tests\Unit\Data\ConsultationDataProvider;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class CreateNewSemesterConsultationUseCaseTest extends TestCase
{
    protected $mockRepo;

    protected $mockAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockAuth = Mockery::mock('alias:Illuminate\Support\Facades\Auth');
        $this->mockRepo = Mockery::mock(ConsultationRepositoryInterface::class);

        $this->mockAuth->shouldReceive('id')->andReturn(1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_weekday_consultation_successfully(): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => WeekTypeEnum::ALL->value,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekdayConsultation')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(true);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    #[DataProviderExternal(ConsultationDataProvider::class, 'weekdayDataProvider')]
    public function it_creates_consultation_for_all_weekdays($weekday, $weekType): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => $weekday,
            'dailyConsultationWeekType' => $weekType,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekdayConsultation')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(true);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    #[DataProviderExternal(ConsultationDataProvider::class, 'weekTypeDataProvider')]
    public function it_creates_consultation_for_different_week_types($weekType): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => $weekType,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekdayConsultation')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(true);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_creates_weekend_consultation_successfully(): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::SATURDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '12.05, 19.05',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekendConsultations')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(2);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(2, $result);
    }

    #[Test]
    public function it_creates_sunday_consultation_successfully(): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::SUNDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '13.05, 20.05',
            'consultationStartTime' => '10:00',
            'consultationEndTime' => '12:00',
            'consultationLocation' => 'Room 102',
        ]);

        $this->mockRepo->shouldReceive('createWeekendConsultations')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(2);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(2, $result);
    }

    #[Test]
    public function it_creates_multiple_weekend_dates_successfully(): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::SATURDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '12.05, 19.05, 26.05, 02.06, 09.06',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekendConsultations')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(5);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(5, $result);
    }

    #[Test]
    public function it_returns_zero_when_weekday_creation_fails(): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => WeekTypeEnum::ALL->value,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekdayConsultation')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(false);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(0, $result);
    }

    #[Test]
    public function it_returns_zero_when_weekend_creation_fails(): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::SATURDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '12.05',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekendConsultations')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(0);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(0, $result);
    }

    #[Test]
    public function it_handles_empty_weekend_dates(): void
    {
        $dto = CreateNewSemesterConsultationDto::from([
            'consultationWeekday' => WeekdayEnum::SATURDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->mockRepo->shouldReceive('createWeekendConsultations')
            ->once()
            ->with(1, 1, Mockery::type(CreateNewSemesterConsultationDto::class))
            ->andReturn(0);

        $useCase = new CreateNewSemesterConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(0, $result);
    }
}
