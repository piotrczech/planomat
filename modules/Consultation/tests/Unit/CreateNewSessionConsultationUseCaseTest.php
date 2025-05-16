<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use Modules\Consultation\Application\UseCases\ScientificWorker\CreateNewSessionConsultationUseCase;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class CreateNewSessionConsultationUseCaseTest extends TestCase
{
    protected $mockRepo;

    protected $mockAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockAuth = Mockery::mock('alias:Illuminate\Support\Facades\Auth');
        $this->mockRepo = Mockery::mock(ConsultationRepositoryInterface::class);

        $this->mockAuth->shouldReceive('id')->andReturn(1); // Załóżmy, że ID zalogowanego pracownika to 1
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_session_consultation_successfully(): void
    {
        $dto = CreateNewSessionConsultationDto::from([
            'consultationDate' => '2025-01-20',
            'consultationStartTime' => '10:00',
            'consultationEndTime' => '11:30',
            'consultationLocation' => 'Sala Sesyjna A',
        ]);

        $this->mockRepo->shouldReceive('createSessionConsultation')
            ->once()
            ->with(1, Mockery::type(CreateNewSessionConsultationDto::class))
            ->andReturn(123); // Załóżmy, że ID nowej konsultacji to 123

        $useCase = new CreateNewSessionConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(123, $result);
    }

    #[Test]
    public function it_returns_zero_when_session_consultation_creation_fails(): void
    {
        $dto = CreateNewSessionConsultationDto::from([
            'consultationDate' => '2025-01-21',
            'consultationStartTime' => '14:00',
            'consultationEndTime' => '15:00',
            'consultationLocation' => 'Sala Sesyjna B',
        ]);

        $this->mockRepo->shouldReceive('createSessionConsultation')
            ->once()
            ->with(1, Mockery::type(CreateNewSessionConsultationDto::class))
            ->andReturn(0); // Repozytorium zwraca 0 w przypadku niepowodzenia

        $useCase = new CreateNewSessionConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(0, $result);
    }

    // Można dodać więcej testów, np. walidujących dane wejściowe w DTO,
    // ale to powinno być obsługiwane przez sam DTO lub Form Requests w Livewire.
    // Tutaj skupiamy się na logice use case.

    #[Test]
    public function it_handles_different_locations_successfully(): void
    {
        $dto = CreateNewSessionConsultationDto::from([
            'consultationDate' => '2025-01-22',
            'consultationStartTime' => '09:00',
            'consultationEndTime' => '10:00',
            'consultationLocation' => 'Online - MS Teams',
        ]);

        $this->mockRepo->shouldReceive('createSessionConsultation')
            ->once()
            ->with(1, Mockery::type(CreateNewSessionConsultationDto::class))
            ->andReturn(124);

        $useCase = new CreateNewSessionConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(124, $result);
    }

    #[Test]
    public function it_handles_different_times_successfully(): void
    {
        $dto = CreateNewSessionConsultationDto::from([
            'consultationDate' => '2025-01-23',
            'consultationStartTime' => '16:30',
            'consultationEndTime' => '18:00',
            'consultationLocation' => 'Gabinet 303',
        ]);

        $this->mockRepo->shouldReceive('createSessionConsultation')
            ->once()
            ->with(1, Mockery::type(CreateNewSessionConsultationDto::class))
            ->andReturn(125);

        $useCase = new CreateNewSessionConsultationUseCase($this->mockRepo);
        $result = $useCase->execute($dto);

        $this->assertEquals(125, $result);
    }
}
