<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use Modules\Consultation\Application\UseCases\ScientificWorker\GetSessionConsultationsUseCase;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class GetSessionConsultationsUseCaseTest extends TestCase
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

        $this->useCase = new GetSessionConsultationsUseCase($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_retrieves_session_consultations_successfully(): void
    {
        $expectedConsultations = [
            [
                'id' => 101,
                'consultation_date' => '2025-01-20',
                'start_time' => '10:00',
                'end_time' => '11:30',
                'location' => 'Sala Sesyjna A',
            ],
            [
                'id' => 102,
                'consultation_date' => '2025-01-22',
                'start_time' => '14:00',
                'end_time' => '15:00',
                'location' => 'Online - MS Teams',
            ],
        ];

        $this->mockRepo->shouldReceive('getSessionConsultations')
            ->once()
            ->with(1) // scientificWorkerId
            ->andReturn($expectedConsultations);

        $result = $this->useCase->execute();

        $this->assertEquals($expectedConsultations, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_empty_array_when_no_session_consultations_found(): void
    {
        $this->mockRepo->shouldReceive('getSessionConsultations')
            ->once()
            ->with(1)
            ->andReturn([]);

        $result = $this->useCase->execute();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_retrieves_consultations_with_various_details(): void
    {
        $expectedConsultations = [
            [
                'id' => 201,
                'consultation_date' => '2025-02-01',
                'start_time' => '09:00',
                'end_time' => '10:00',
                'location' => 'Gabinet Dziekana',
            ],
            [
                'id' => 202,
                'consultation_date' => '2025-02-03',
                'start_time' => '11:00',
                'end_time' => '12:30',
                'location' => 'Laboratorium Komputerowe 3',
            ],
            [
                'id' => 203,
                'consultation_date' => '2025-02-05',
                'start_time' => '15:00',
                'end_time' => '16:00',
                'location' => 'Biblioteka, Czytelnia Główna',
            ],
        ];

        $this->mockRepo->shouldReceive('getSessionConsultations')
            ->once()
            ->with(1) // scientificWorkerId
            ->andReturn($expectedConsultations);

        $result = $this->useCase->execute();

        $this->assertEquals($expectedConsultations, $result);
        $this->assertCount(3, $result);
        $this->assertEquals('Gabinet Dziekana', $result[0]['location']);
    }
    // W przyszłości, jeśli GetSessionConsultationsUseCase będzie przyjmował parametry
    // (np. zakres dat), należałoby dodać testy sprawdzające filtrowanie.
}
