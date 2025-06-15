<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Dto;

use App\Domain\Dto\StoreSemesterDto;
use App\Domain\Enums\SemesterSeasonEnum;
use App\Infrastructure\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StoreSemesterDtoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_valid_dto_with_correct_data(): void
    {
        $data = [
            'start_year' => 2023,
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-02-28',
        ];

        $dto = StoreSemesterDto::from($data);

        $this->assertInstanceOf(StoreSemesterDto::class, $dto);
        $this->assertEquals($data['start_year'], $dto->start_year);
        $this->assertEquals($data['season'], $dto->season);
        $this->assertEquals($data['semester_start_date'], $dto->semester_start_date);
        $this->assertEquals($data['session_start_date'], $dto->session_start_date);
        $this->assertEquals($data['end_date'], $dto->end_date);
    }

    #[Test]
    public function it_throws_validation_exception_for_missing_required_fields(): void
    {
        $this->expectException(ValidationException::class);
        StoreSemesterDto::from([]);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_start_year_format(): void
    {
        $this->expectException(ValidationException::class);
        StoreSemesterDto::from([
            'start_year' => 'abc', // Invalid: not an integer
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-02-28',
        ]);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_season(): void
    {
        $this->expectException(ValidationException::class);
        StoreSemesterDto::from([
            'start_year' => 2023,
            'season' => 'INVALID_SEASON',
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-02-28',
        ]);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_date_format(): void
    {
        $this->expectException(ValidationException::class);
        StoreSemesterDto::from([
            'start_year' => 2023,
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '01-10-2023', // Invalid format
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-02-28',
        ]);
    }

    #[Test]
    public function it_throws_validation_exception_if_session_starts_before_semester(): void
    {
        $this->expectException(ValidationException::class);
        StoreSemesterDto::from([
            'start_year' => 2023,
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2023-09-15', // Invalid: before semester_start_date
            'end_date' => '2024-02-28',
        ]);
    }

    #[Test]
    public function it_throws_validation_exception_if_end_date_is_before_session_start(): void
    {
        $this->expectException(ValidationException::class);
        StoreSemesterDto::from([
            'start_year' => 2023,
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-01-10', // Invalid: before session_start_date
        ]);
    }
}
