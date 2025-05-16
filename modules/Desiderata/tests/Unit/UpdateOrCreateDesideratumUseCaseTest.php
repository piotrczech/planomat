<?php

declare(strict_types=1);

namespace Modules\Desiderata\Tests\Unit;

use Modules\Desiderata\Application\UseCases\ScientificWorker\UpdateOrCreateDesideratumUseCase;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Tests\Unit\Data\DesideratumDataProvider;
use Spatie\LaravelData\Optional;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProviderExternal;

class UpdateOrCreateDesideratumUseCaseTest extends TestCase
{
    protected $mockRepo;

    protected $mockAuthService;

    protected $mockAuthFacade;

    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepo = Mockery::mock(DesideratumRepositoryInterface::class);

        $this->mockAuthFacade = Mockery::mock('alias:Illuminate\Support\Facades\Auth');
        $this->mockAuthFacade->shouldReceive('id')->andReturn(1);

        $this->useCase = new UpdateOrCreateDesideratumUseCase($this->mockRepo, $this->mockAuthService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_desideratum_successfully(): void
    {
        $dto = UpdateOrCreateDesideratumDto::from([
            'wantStationary' => true,
            'wantNonStationary' => false,
            'agreeToOvertime' => false,
            'unwantedCourseIds' => [1, 2],
            'wantedCourseIds' => [3, 4],
            'proficientCourseIds' => [1, 2, 3, 4, 5],
            'masterThesesCount' => 2,
            'bachelorThesesCount' => 1,
            'maxHoursPerDay' => 6,
            'maxConsecutiveHours' => 4,
            'unavailableTimeSlots' => ['Monday_8:00', 'Monday_9:00'],
            'additionalNotes' => 'Some notes',
        ]);

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn(1);

        $result = $this->useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    #[DataProviderExternal(DesideratumDataProvider::class, 'desideratumConfigurationsProvider')]
    public function it_handles_different_desideratum_configurations($dtoData): void
    {
        $dto = UpdateOrCreateDesideratumDto::from($dtoData);

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn(1);

        $result = $this->useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_updates_existing_desideratum(): void
    {
        $dto = UpdateOrCreateDesideratumDto::from([
            'wantStationary' => true,
            'wantNonStationary' => true,
            'agreeToOvertime' => true,
            'unwantedCourseIds' => [2],
            'wantedCourseIds' => [1, 3, 4],
            'proficientCourseIds' => [1, 2, 3, 4],
            'masterThesesCount' => 3,
            'bachelorThesesCount' => 2,
            'maxHoursPerDay' => 8,
            'maxConsecutiveHours' => 3,
            'unavailableTimeSlots' => ['Wednesday_12:00'],
            'additionalNotes' => 'Updated notes',
        ]);

        $existingId = 5;

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn($existingId);

        $result = $this->useCase->execute($dto);

        $this->assertEquals($existingId, $result);
    }

    #[Test]
    public function it_handles_empty_courses_lists(): void
    {
        $dto = UpdateOrCreateDesideratumDto::from([
            'wantStationary' => true,
            'wantNonStationary' => true,
            'agreeToOvertime' => false,
            'unwantedCourseIds' => [],
            'wantedCourseIds' => [],
            'proficientCourseIds' => [],
            'masterThesesCount' => 0,
            'bachelorThesesCount' => 0,
            'maxHoursPerDay' => 6,
            'maxConsecutiveHours' => 3,
            'unavailableTimeSlots' => [],
            'additionalNotes' => '',
        ]);

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn(1);

        $result = $this->useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    #[DataProviderExternal(DesideratumDataProvider::class, 'maximumUnavailableSlotsProvider')]
    public function it_handles_maximum_unavailable_time_slots($dtoData): void
    {
        $dto = UpdateOrCreateDesideratumDto::from($dtoData);

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn(1);

        $result = $this->useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_handles_zero_max_hours(): void
    {
        // Edge case: 0 hours per day and 0 consecutive hours
        $dto = UpdateOrCreateDesideratumDto::from([
            'wantStationary' => true,
            'wantNonStationary' => false,
            'agreeToOvertime' => false,
            'unwantedCourseIds' => [],
            'wantedCourseIds' => [1],
            'proficientCourseIds' => [1, 2],
            'masterThesesCount' => 0,
            'bachelorThesesCount' => 0,
            'maxHoursPerDay' => 0,
            'maxConsecutiveHours' => 0,
            'unavailableTimeSlots' => [],
            'additionalNotes' => 'Zero hours',
        ]);

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn(1);

        $result = $this->useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_handles_missing_optional_fields(): void
    {
        // Test with only required fields, all optional fields as Optional instances
        $dto = new UpdateOrCreateDesideratumDto(
            wantStationary: true,
            wantNonStationary: false,
            agreeToOvertime: false,
            unwantedCourseIds: new Optional,
            wantedCourseIds: new Optional,
            proficientCourseIds: new Optional,
            masterThesesCount: new Optional,
            bachelorThesesCount: new Optional,
            maxHoursPerDay: new Optional,
            maxConsecutiveHours: new Optional,
            unavailableTimeSlots: new Optional,
            additionalNotes: new Optional,
        );

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn(1);

        $result = $this->useCase->execute($dto);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_returns_zero_when_creation_fails(): void
    {
        $dto = UpdateOrCreateDesideratumDto::from([
            'wantStationary' => true,
            'wantNonStationary' => false,
            'agreeToOvertime' => false,
        ]);

        $this->mockRepo->shouldReceive('updateOrCreate')
            ->once()
            ->with(Mockery::type(UpdateOrCreateDesideratumDto::class))
            ->andReturn(0);

        $result = $this->useCase->execute($dto);

        $this->assertEquals(0, $result);
    }
}
