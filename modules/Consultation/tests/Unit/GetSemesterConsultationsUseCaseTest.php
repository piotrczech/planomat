<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use Modules\Consultation\Application\UseCases\ScientificWorker\GetSemesterConsultationsUseCase;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class GetSemesterConsultationsUseCaseTest extends TestCase
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

        $this->useCase = new GetSemesterConsultationsUseCase($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_retrieves_semester_consultations_successfully(): void
    {
        $expectedConsultations = [
            [
                'id' => 1,
                'weekday' => 0,
                'startTime' => '09:00',
                'endTime' => '10:30',
                'location' => 'Room 101',
                'weekType' => 'every',
            ],
            [
                'id' => 2,
                'weekday' => 2,
                'startTime' => '13:15',
                'endTime' => '14:45',
                'location' => 'Conference Room',
                'weekType' => 'even',
            ],
        ];

        $this->mockRepo->shouldReceive('getSemesterConsultations')
            ->once()
            ->with(1, 2) // scientificWorkerId = 1, semesterId = 2
            ->andReturn($expectedConsultations);

        $result = $this->useCase->execute(2);

        $this->assertEquals($expectedConsultations, $result);
    }

    #[Test]
    public function it_returns_empty_array_when_no_consultations_found(): void
    {
        $this->mockRepo->shouldReceive('getSemesterConsultations')
            ->once()
            ->with(1, 2)
            ->andReturn([]);

        $result = $this->useCase->execute(2);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_correctly_handles_weekday_consultations(): void
    {
        $expectedConsultations = [
            [
                'id' => 1,
                'weekday' => 0, // Monday
                'startTime' => '09:00',
                'endTime' => '10:30',
                'location' => 'Room 101',
                'weekType' => 'every',
            ],
            [
                'id' => 2,
                'weekday' => 2, // Wednesday
                'startTime' => '13:15',
                'endTime' => '14:45',
                'location' => 'Conference Room',
                'weekType' => 'even',
            ],
            [
                'id' => 3,
                'weekday' => 4, // Friday
                'startTime' => '15:00',
                'endTime' => '16:30',
                'location' => 'Room 105',
                'weekType' => 'odd',
            ],
        ];

        $this->mockRepo->shouldReceive('getSemesterConsultations')
            ->once()
            ->with(1, 3)
            ->andReturn($expectedConsultations);

        $result = $this->useCase->execute(3);

        $this->assertCount(3, $result);
        $this->assertEquals($expectedConsultations, $result);

        foreach ($result as $consultation) {
            $this->assertLessThanOrEqual(4, $consultation['weekday']);
            $this->assertGreaterThanOrEqual(0, $consultation['weekday']);
        }
    }

    #[Test]
    public function it_correctly_handles_weekend_consultations(): void
    {
        $expectedConsultations = [
            [
                'id' => 5,
                'weekday' => 5, // Saturday
                'startTime' => '10:00',
                'endTime' => '12:00',
                'location' => 'Room 201',
                'weekType' => 'specific',
                'date' => '2023-05-13',
            ],
            [
                'id' => 6,
                'weekday' => 6, // Sunday
                'startTime' => '11:00',
                'endTime' => '13:00',
                'location' => 'Room 202',
                'weekType' => 'specific',
                'date' => '2023-05-14',
            ],
        ];

        $this->mockRepo->shouldReceive('getSemesterConsultations')
            ->once()
            ->with(1, 3)
            ->andReturn($expectedConsultations);

        $result = $this->useCase->execute(3);

        $this->assertCount(2, $result);
        $this->assertEquals($expectedConsultations, $result);

        foreach ($result as $consultation) {
            $this->assertGreaterThanOrEqual(5, $consultation['weekday']);
            $this->assertLessThanOrEqual(6, $consultation['weekday']);
            $this->assertEquals('specific', $consultation['weekType']);
            $this->assertArrayHasKey('date', $consultation);
        }
    }

    #[Test]
    public function it_correctly_handles_mixed_consultations(): void
    {
        $expectedConsultations = [
            // Weekdays
            [
                'id' => 1,
                'weekday' => 0, // Monday
                'startTime' => '09:00',
                'endTime' => '10:30',
                'location' => 'Room 101',
                'weekType' => 'every',
            ],
            [
                'id' => 3,
                'weekday' => 4, // Friday
                'startTime' => '15:00',
                'endTime' => '16:30',
                'location' => 'Room 105',
                'weekType' => 'odd',
            ],
            // Weekend
            [
                'id' => 5,
                'weekday' => 5, // Saturday
                'startTime' => '10:00',
                'endTime' => '12:00',
                'location' => 'Room 201',
                'weekType' => 'specific',
                'date' => '2023-05-13',
            ],
        ];

        $this->mockRepo->shouldReceive('getSemesterConsultations')
            ->once()
            ->with(1, 4)
            ->andReturn($expectedConsultations);

        $result = $this->useCase->execute(4);

        $this->assertCount(3, $result);
        $this->assertEquals($expectedConsultations, $result);

        $weekdayCount = 0;
        $weekendCount = 0;

        foreach ($result as $consultation) {
            if ($consultation['weekday'] <= 4) {
                $weekdayCount++;
                $this->assertArrayNotHasKey('date', $consultation);
            } else {
                $weekendCount++;
                $this->assertArrayHasKey('date', $consultation);
                $this->assertEquals('specific', $consultation['weekType']);
            }
        }

        $this->assertEquals(2, $weekdayCount);
        $this->assertEquals(1, $weekendCount);
    }

    #[Test]
    public function it_handles_different_week_types_correctly(): void
    {
        $expectedConsultations = [
            [
                'id' => 1,
                'weekday' => 0,
                'startTime' => '09:00',
                'endTime' => '10:30',
                'location' => 'Room 101',
                'weekType' => 'every',
            ],
            [
                'id' => 2,
                'weekday' => 2,
                'startTime' => '13:15',
                'endTime' => '14:45',
                'location' => 'Conference Room',
                'weekType' => 'even',
            ],
            [
                'id' => 3,
                'weekday' => 4,
                'startTime' => '15:00',
                'endTime' => '16:30',
                'location' => 'Room 105',
                'weekType' => 'odd',
            ],
        ];

        $this->mockRepo->shouldReceive('getSemesterConsultations')
            ->once()
            ->with(1, 5)
            ->andReturn($expectedConsultations);

        $result = $this->useCase->execute(5);

        $this->assertCount(3, $result);

        $weekTypeCount = [
            'every' => 0,
            'even' => 0,
            'odd' => 0,
        ];

        foreach ($result as $consultation) {
            $weekTypeCount[$consultation['weekType']]++;
        }

        $this->assertEquals(1, $weekTypeCount['every']);
        $this->assertEquals(1, $weekTypeCount['even']);
        $this->assertEquals(1, $weekTypeCount['odd']);
    }
}
