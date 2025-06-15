<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCases\Semester;

use App\Application\UseCases\Semester\DeleteSemesterUseCase;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @mixin \Mockery\LegacyMockInterface
 * @mixin \Mockery\MockInterface
 */
class DeleteSemesterUseCaseTest extends TestCase
{
    protected SemesterRepositoryInterface $semesterRepositoryMock;

    protected DeleteSemesterUseCase $deleteSemesterUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $this->deleteSemesterUseCase = new DeleteSemesterUseCase($this->semesterRepositoryMock);
    }

    #[Test]
    public function it_deletes_semester_successfully(): void
    {
        $existingSemesterId = 1;

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('delete')
            ->once()
            ->with($existingSemesterId)
            ->andReturn(true);

        $result = $this->deleteSemesterUseCase->execute($existingSemesterId);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_throws_model_not_found_exception_when_deleting_non_existing_semester(): void
    {
        $nonExistingId = 999;

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('delete')
            ->once()
            ->with($nonExistingId)
            ->andThrow(new ModelNotFoundException);

        $this->expectException(ModelNotFoundException::class);
        $this->deleteSemesterUseCase->execute($nonExistingId);
    }

    #[Test]
    public function it_returns_false_when_semester_deletion_fails_in_repository(): void
    {
        $existingSemesterId = 1;

        /** @var \Mockery\MockInterface $mock */
        $mock = $this->semesterRepositoryMock;
        $mock->shouldReceive('delete')
            ->once()
            ->with($existingSemesterId)
            ->andReturn(false);

        $result = $this->deleteSemesterUseCase->execute($existingSemesterId);

        $this->assertFalse($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
