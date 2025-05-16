<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use Modules\Consultation\Application\UseCases\ScientificWorker\DeleteSessionConsultationUseCase;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use TypeError;

class DeleteSessionConsultationUseCaseTest extends TestCase
{
    protected $mockAuth;

    protected $mockRepo;

    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockAuth = Mockery::mock('alias:Illuminate\Support\Facades\Auth');
        $this->mockRepo = Mockery::mock(ConsultationRepositoryInterface::class);

        $this->mockAuth->shouldReceive('id')->andReturn(1); // Załóżmy, że ID zalogowanego pracownika to 1

        $this->useCase = new DeleteSessionConsultationUseCase($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_deletes_session_consultation_successfully(): void
    {
        $consultationId = 50;
        $this->mockRepo->shouldReceive('deleteSessionConsultation')
            ->once()
            ->with($consultationId, 1) // consultationId, scientificWorkerId
            ->andReturn(true);

        $result = $this->useCase->execute($consultationId);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_session_consultation_not_found(): void
    {
        $consultationId = 999; // Nieistniejące ID
        $this->mockRepo->shouldReceive('deleteSessionConsultation')
            ->once()
            ->with($consultationId, 1)
            ->andReturn(false);

        $result = $this->useCase->execute($consultationId);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_false_when_session_consultation_belongs_to_another_user(): void
    {
        $consultationId = 51;
        // Symulacja, że repozytorium zwraca false, gdy konsultacja nie należy do użytkownika
        $this->mockRepo->shouldReceive('deleteSessionConsultation')
            ->once()
            ->with($consultationId, 1)
            ->andReturn(false);

        $result = $this->useCase->execute($consultationId);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_handles_invalid_session_consultation_id(): void
    {
        $invalidIds = [0, -1];

        foreach ($invalidIds as $invalidId) {
            $this->mockRepo->shouldReceive('deleteSessionConsultation')
                ->once()
                ->with($invalidId, 1)
                ->andReturn(false);

            $result = $this->useCase->execute($invalidId);

            $this->assertFalse($result, "Deletion with ID {$invalidId} should return false");
        }
    }

    #[Test]
    public function it_throws_type_error_for_null_session_consultation_id(): void
    {
        $this->expectException(TypeError::class);
        $this->useCase->execute(null);
    }
}
