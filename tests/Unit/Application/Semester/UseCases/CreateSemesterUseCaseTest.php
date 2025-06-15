<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCases\Semester;

use App\Application\UseCases\Semester\CreateSemesterUseCase;
use App\Domain\Dto\StoreSemesterDto;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Models\Semester;
use App\Domain\Enums\SemesterSeasonEnum;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

/**
 * @mixin \Mockery\LegacyMockInterface
 * @mixin \Mockery\MockInterface
 */
class CreateSemesterUseCaseTest extends TestCase
{
    protected SemesterRepositoryInterface $semesterRepositoryMock;

    protected CreateSemesterUseCase $createSemesterUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $this->createSemesterUseCase = new CreateSemesterUseCase($this->semesterRepositoryMock);
    }

    #[Test]
    public function it_creates_semester_successfully(): void
    {
        $data = [
            'start_year' => 2023,
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-02-28',
        ];
        $dto = StoreSemesterDto::from($data);

        $createdSemester = new Semester($data);
        $createdSemester->id = 1; // Symulacja ID po zapisie

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($argument) use ($dto) {
                return $argument instanceof StoreSemesterDto &&
                       $argument->start_year === $dto->start_year &&
                       $argument->season === $dto->season &&
                       $argument->semester_start_date === $dto->semester_start_date &&
                       $argument->session_start_date === $dto->session_start_date &&
                       $argument->end_date === $dto->end_date;
            }))
            ->andReturn($createdSemester);

        $result = $this->createSemesterUseCase->execute($dto);

        $this->assertInstanceOf(Semester::class, $result);
        $this->assertEquals($createdSemester->id, $result->id);
        $this->assertEquals($dto->start_year, $result->start_year);
        $this->assertEquals(SemesterSeasonEnum::from((int) $dto->season), $result->season);
        $this->assertEquals($dto->semester_start_date, $result->semester_start_date->format('Y-m-d'));
        $this->assertEquals($dto->session_start_date, $result->session_start_date->format('Y-m-d'));
        $this->assertEquals($dto->end_date, $result->end_date->format('Y-m-d'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
