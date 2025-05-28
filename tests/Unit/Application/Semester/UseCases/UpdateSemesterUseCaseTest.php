<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Semester\UseCases;

use App\Application\Semester\UseCases\UpdateSemesterUseCase;
use App\Domain\Semester\Dto\UpdateSemesterDto;
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
class UpdateSemesterUseCaseTest extends TestCase
{
    protected SemesterRepositoryInterface $semesterRepositoryMock;

    protected UpdateSemesterUseCase $updateSemesterUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $this->updateSemesterUseCase = new UpdateSemesterUseCase($this->semesterRepositoryMock);
    }

    #[Test]
    public function it_updates_semester_successfully(): void
    {
        $existingSemesterId = 1;
        $updateData = [
            'id' => $existingSemesterId,
            'start_year' => 2024,
            'season' => SemesterSeasonEnum::SPRING->value,
            'semester_start_date' => '2024-03-01',
            'session_start_date' => '2024-06-15',
            'end_date' => '2024-09-30',
        ];
        $dto = UpdateSemesterDto::from($updateData);

        $updatedSemesterModel = new Semester($updateData);
        $updatedSemesterModel->id = $existingSemesterId;
        $updatedSemesterModel->season = SemesterSeasonEnum::from($updateData['season']);
        $updatedSemesterModel->semester_start_date = \Carbon\Carbon::parse($updateData['semester_start_date']);
        $updatedSemesterModel->session_start_date = \Carbon\Carbon::parse($updateData['session_start_date']);
        $updatedSemesterModel->end_date = \Carbon\Carbon::parse($updateData['end_date']);

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('update')
            ->once()
            ->with($existingSemesterId, Mockery::on(function ($argument) use ($dto) {
                return $argument instanceof UpdateSemesterDto &&
                       $argument->id === $dto->id &&
                       $argument->start_year === $dto->start_year &&
                       $argument->season === $dto->season &&
                       $argument->semester_start_date === $dto->semester_start_date &&
                       $argument->session_start_date === $dto->session_start_date &&
                       $argument->end_date === $dto->end_date;
            }))
            ->andReturn($updatedSemesterModel);

        $result = $this->updateSemesterUseCase->execute($existingSemesterId, $dto);

        $this->assertInstanceOf(Semester::class, $result);
        $this->assertEquals($dto->id, $result->id);
        $this->assertEquals($dto->start_year, $result->start_year);
        $this->assertEquals(SemesterSeasonEnum::from((int) $dto->season), $result->season);
        $this->assertEquals($dto->semester_start_date, $result->semester_start_date->format('Y-m-d'));
        $this->assertEquals($dto->session_start_date, $result->session_start_date->format('Y-m-d'));
        $this->assertEquals($dto->end_date, $result->end_date->format('Y-m-d'));
    }

    #[Test]
    public function it_throws_model_not_found_exception_when_updating_non_existing_semester(): void
    {
        $nonExistingId = 999;
        $updateData = [
            'id' => $nonExistingId,
            'start_year' => 2024,
            'season' => SemesterSeasonEnum::SPRING->value,
            'semester_start_date' => '2024-03-01',
            'session_start_date' => '2024-06-15',
            'end_date' => '2024-09-30',
        ];
        $dto = UpdateSemesterDto::from($updateData);

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('update')
            ->once()
            ->with($nonExistingId, $dto)
            ->andThrow(new ModelNotFoundException);

        $this->expectException(ModelNotFoundException::class);
        $this->updateSemesterUseCase->execute($nonExistingId, $dto);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
