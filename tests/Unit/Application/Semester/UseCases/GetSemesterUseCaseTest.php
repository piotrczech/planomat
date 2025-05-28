<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Semester\UseCases;

use App\Application\Semester\UseCases\GetSemesterUseCase;
use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Models\Semester;
use App\Enums\SemesterSeasonEnum;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @mixin \Mockery\LegacyMockInterface
 * @mixin \Mockery\MockInterface
 */
class GetSemesterUseCaseTest extends TestCase
{
    protected SemesterRepositoryInterface $semesterRepositoryMock;

    protected GetSemesterUseCase $getSemesterUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $this->getSemesterUseCase = new GetSemesterUseCase($this->semesterRepositoryMock);
    }

    #[Test]
    public function it_retrieves_semester_successfully(): void
    {
        $semesterId = 1;
        $semesterData = [
            'id' => $semesterId,
            'start_year' => 2023,
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-02-28',
        ];

        $semesterModel = new Semester($semesterData);
        $semesterModel->id = $semesterId;
        $semesterModel->season = SemesterSeasonEnum::from($semesterData['season']);
        $semesterModel->semester_start_date = \Carbon\Carbon::parse($semesterData['semester_start_date']);
        $semesterModel->session_start_date = \Carbon\Carbon::parse($semesterData['session_start_date']);
        $semesterModel->end_date = \Carbon\Carbon::parse($semesterData['end_date']);

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('findById')
            ->once()
            ->with($semesterId)
            ->andReturn($semesterModel);

        $result = $this->getSemesterUseCase->execute($semesterId);

        $this->assertInstanceOf(Semester::class, $result);
        $this->assertEquals($semesterModel->id, $result->id);
        $this->assertEquals($semesterModel->start_year, $result->start_year);
        $this->assertEquals($semesterModel->season, $result->season);
        $this->assertEquals($semesterModel->semester_start_date->format('Y-m-d'), $result->semester_start_date->format('Y-m-d'));
        $this->assertEquals($semesterModel->session_start_date->format('Y-m-d'), $result->session_start_date->format('Y-m-d'));
        $this->assertEquals($semesterModel->end_date->format('Y-m-d'), $result->end_date->format('Y-m-d'));
    }

    #[Test]
    public function it_throws_model_not_found_exception_when_retrieving_non_existing_semester(): void
    {
        $nonExistingId = 999;

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('findById')
            ->once()
            ->with($nonExistingId)
            ->andThrow(new ModelNotFoundException);

        $this->expectException(ModelNotFoundException::class);
        $this->getSemesterUseCase->execute($nonExistingId);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
