<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Dto;

use App\Domain\Dto\UpdateSemesterDto;
use App\Domain\Enums\SemesterSeasonEnum;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdateSemesterDtoTest extends TestCase
{
    #[Test]
    public function it_creates_valid_dto_with_correct_data_for_update(): void
    {
        $data = [
            'id' => 1,
            'start_year' => 2024,
            'season' => SemesterSeasonEnum::SPRING->value,
            'semester_start_date' => '2024-03-01',
            'session_start_date' => '2024-06-15',
            'end_date' => '2024-09-30',
        ];

        $dto = UpdateSemesterDto::from($data);

        $this->assertInstanceOf(UpdateSemesterDto::class, $dto);
        $this->assertEquals($data['id'], $dto->id);
        $this->assertEquals($data['start_year'], $dto->start_year);
        $this->assertEquals($data['season'], $dto->season);
        $this->assertEquals($data['semester_start_date'], $dto->semester_start_date);
        $this->assertEquals($data['session_start_date'], $dto->session_start_date);
        $this->assertEquals($data['end_date'], $dto->end_date);
    }

    #[Test]
    public function it_throws_validation_exception_for_missing_id_on_update(): void
    {
        $this->expectException(ValidationException::class);
        UpdateSemesterDto::from([
            'start_year' => 2024,
            'season' => SemesterSeasonEnum::SPRING->value,
            'semester_start_date' => '2024-03-01',
            'session_start_date' => '2024-06-15',
            'end_date' => '2024-09-30',
        ]);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_id_format_on_update(): void
    {
        $this->expectException(ValidationException::class);
        UpdateSemesterDto::from([
            'id' => 'not-an-integer',
            'start_year' => 2024,
            'season' => SemesterSeasonEnum::SPRING->value,
            'semester_start_date' => '2024-03-01',
            'session_start_date' => '2024-06-15',
            'end_date' => '2024-09-30',
        ]);
    }

    #[Test]
    public function it_throws_validation_exception_for_non_existing_id_on_update(): void
    {
        $this->expectException(ValidationException::class);
        UpdateSemesterDto::from([
            'id' => 99999,
            'start_year' => 2024,
            'season' => SemesterSeasonEnum::SPRING->value,
            'semester_start_date' => '2024-03-01',
            'session_start_date' => '2024-06-15',
            'end_date' => '2024-09-30',
        ]);
    }
}
