<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Semester\UseCases;

use App\Application\Semester\UseCases\GetCurrentSemesterDatesUseCase;
use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Models\Semester;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetCurrentSemesterDatesUseCaseTest extends TestCase
{
    protected $semesterRepositoryMock;

    protected GetCurrentSemesterDatesUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $this->useCase = new GetCurrentSemesterDatesUseCase($this->semesterRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_returns_current_semester_dates_as_array(): void
    {
        $sessionStartDate = Carbon::parse('2024-01-15');
        $endDate = Carbon::parse('2024-02-28');

        $semester = new Semester([
            'id' => 1,
            'start_year' => 2023,
            'season' => 'WINTER',
            'semester_start_date' => Carbon::parse('2023-10-01'),
            'session_start_date' => $sessionStartDate,
            'end_date' => $endDate,
        ]);

        $this->semesterRepositoryMock
            ->shouldReceive('findCurrentSemester')
            ->once()
            ->andReturn($semester);

        $result = $this->useCase->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('session_start_date', $result);
        $this->assertArrayHasKey('end_date', $result);
        $this->assertEquals($sessionStartDate->toDateString(), $result['session_start_date']);
        $this->assertEquals($endDate->toDateString(), $result['end_date']);
    }

    public function test_execute_returns_null_when_no_current_semester(): void
    {
        $this->semesterRepositoryMock
            ->shouldReceive('findCurrentSemester')
            ->once()
            ->andReturn(null);

        $result = $this->useCase->execute();

        $this->assertNull($result);
    }
}
