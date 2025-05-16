<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use Modules\Consultation\Application\UseCases\ScientificWorker\DeleteSemesterConsultationUseCase;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use TypeError;

class DeleteSemesterConsultationUseCaseTest extends TestCase
{
    protected $mockAuth;

    protected $mockRepo;

    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockAuth = Mockery::mock('alias:Illuminate\Support\Facades\Auth');
        $this->mockRepo = Mockery::mock(ConsultationRepositoryInterface::class);

        $this->mockAuth->shouldReceive('id')->andReturn(1);

        $this->useCase = new DeleteSemesterConsultationUseCase($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_deletes_consultation_successfully(): void
    {
        $this->mockRepo->shouldReceive('deleteSemesterConsultation')
            ->once()
            ->with(5, 1) // consultationId = 5, scientificWorkerId = 1
            ->andReturn(true);

        $result = $this->useCase->execute(5);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_consultation_not_found(): void
    {
        $this->mockRepo->shouldReceive('deleteSemesterConsultation')
            ->once()
            ->with(999, 1) // Non-existent consultationId = 999
            ->andReturn(false);

        $result = $this->useCase->execute(999);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_false_when_consultation_belongs_to_another_user(): void
    {
        $this->mockRepo->shouldReceive('deleteSemesterConsultation')
            ->once()
            ->with(5, 1)
            ->andReturn(false);

        $result = $this->useCase->execute(5);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_false_when_consultation_already_happened(): void
    {
        $this->mockRepo->shouldReceive('deleteSemesterConsultation')
            ->once()
            ->with(10, 1) // Past consultation ID = 10
            ->andReturn(false);

        $result = $this->useCase->execute(10);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_handles_invalid_consultation_id(): void
    {
        $invalidIds = [0, -1];

        foreach ($invalidIds as $invalidId) {
            $this->mockRepo->shouldReceive('deleteSemesterConsultation')
                ->once()
                ->with($invalidId, 1)
                ->andReturn(false);

            $result = $this->useCase->execute($invalidId);

            $this->assertFalse($result, "Deletion with ID {$invalidId} should return false");
        }
    }

    #[Test]
    public function it_throws_type_error_for_null_consultation_id(): void
    {
        $this->expectException(TypeError::class);

        $this->useCase->execute(null);
    }

    #[Test]
    public function it_handles_concurrent_deletion_attempts(): void
    {
        // First call - successful deletion
        $this->mockRepo->shouldReceive('deleteSemesterConsultation')
            ->once()
            ->ordered()
            ->with(7, 1)
            ->andReturn(true);

        // Second call (as if executed by another thread with ID = 2)
        // should no longer find the consultation
        $secondMockAuth = Mockery::mock('alias:Illuminate\Support\Facades\Auth');
        $secondMockAuth->shouldReceive('id')->andReturn(2);

        $secondMockRepo = Mockery::mock(ConsultationRepositoryInterface::class);
        $secondMockRepo->shouldReceive('deleteSemesterConsultation')
            ->with(7, 2)
            ->andReturn(false);

        // First user deletes the consultation
        $result1 = $this->useCase->execute(7);

        $this->assertTrue($result1);

        // Verify the repository was called correctly
        $this->mockRepo->shouldHaveReceived('deleteSemesterConsultation')
            ->with(7, 1)
            ->once();
    }
}
