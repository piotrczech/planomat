<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;
use Illuminate\Validation\ValidationException;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CreateNewSemesterConsultationDtoTest extends TestCase
{
    #[Test]
    public function it_validates_required_fields(): void
    {
        $this->expectException(ValidationException::class);

        CreateNewSemesterConsultationDto::validateAndCreate([]);
    }

    #[Test]
    public function it_creates_valid_weekday_consultation_dto(): void
    {
        $dto = CreateNewSemesterConsultationDto::validateAndCreate([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => WeekTypeEnum::ALL->value,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->assertInstanceOf(CreateNewSemesterConsultationDto::class, $dto);
        $this->assertEquals(WeekdayEnum::MONDAY->value, $dto->consultationWeekday);
        $this->assertEquals(WeekTypeEnum::ALL->value, $dto->dailyConsultationWeekType);
        $this->assertEquals('09:00', $dto->consultationStartTime);
        $this->assertEquals('10:30', $dto->consultationEndTime);
        $this->assertEquals('Room 101', $dto->consultationLocation);
    }

    #[Test]
    public function it_creates_valid_weekend_consultation_dto(): void
    {
        $dto = CreateNewSemesterConsultationDto::validateAndCreate([
            'consultationWeekday' => WeekdayEnum::SATURDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '12.05, 19.05',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);

        $this->assertInstanceOf(CreateNewSemesterConsultationDto::class, $dto);
        $this->assertEquals(WeekdayEnum::SATURDAY->value, $dto->consultationWeekday);
        $this->assertEquals('12.05, 19.05', $dto->weeklyConsultationDates);
    }

    #[Test]
    public function it_validates_weekday_type_for_weekdays(): void
    {
        $this->expectException(ValidationException::class);

        // Nie podajemy typu tygodnia dla dnia roboczego
        CreateNewSemesterConsultationDto::validateAndCreate([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => '', // Brak wymaganego pola
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);
    }

    #[Test]
    public function it_validates_dates_for_weekend(): void
    {
        $this->expectException(ValidationException::class);

        // Nie podajemy dat dla weekendu
        CreateNewSemesterConsultationDto::validateAndCreate([
            'consultationWeekday' => WeekdayEnum::SATURDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '', // Brak wymaganego pola
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);
    }

    #[Test]
    public function it_validates_consultation_times(): void
    {
        $this->expectException(ValidationException::class);

        // Czas zakończenia przed czasem rozpoczęcia
        CreateNewSemesterConsultationDto::validateAndCreate([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => WeekTypeEnum::ALL->value,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '10:00',
            'consultationEndTime' => '09:00', // Błędny czas
            'consultationLocation' => 'Room 101',
        ]);
    }

    #[Test]
    public function it_validates_location_length(): void
    {
        $this->expectException(ValidationException::class);

        // Lokalizacja za krótka
        CreateNewSemesterConsultationDto::validateAndCreate([
            'consultationWeekday' => WeekdayEnum::MONDAY->value,
            'dailyConsultationWeekType' => WeekTypeEnum::ALL->value,
            'weeklyConsultationDates' => '',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'A', // Za krótka
        ]);
    }

    #[Test]
    public function it_validates_consultation_dates_format(): void
    {
        $this->expectException(ValidationException::class);

        // Nieprawidłowy format dat (powinno być DD.MM)
        CreateNewSemesterConsultationDto::validateAndCreate([
            'consultationWeekday' => WeekdayEnum::SATURDAY->value,
            'dailyConsultationWeekType' => '',
            'weeklyConsultationDates' => '2023-05-12', // Nieprawidłowy format
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:30',
            'consultationLocation' => 'Room 101',
        ]);
    }
}
