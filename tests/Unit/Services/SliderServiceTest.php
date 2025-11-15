<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Services\SliderService;
use App\Models\Slider;
use Illuminate\Database\Eloquent\Collection;

class SliderServiceTest extends TestCase
{
    private SliderService $sliderService;
    private MockObject $sliderModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Slider model
        $this->sliderModel = $this->createMock(Slider::class);
        $this->sliderService = new SliderService();

        // We'll need to inject the mock somehow - for now using reflection
        $reflection = new \ReflectionClass($this->sliderService);
        $property = $reflection->getProperty('model');
        if ($property->isInitialized($this->sliderService)) {
            $property->setAccessible(true);
            $property->setValue($this->sliderService, $this->sliderModel);
        }
    }

    public function testGetSliderByIdReturnsSlider()
    {
        $sliderId = 1;
        $expectedSlider = new Slider(['slider_id' => $sliderId, 'slider_title' => 'Test Slider']);

        $this->sliderModel->expects($this->once())
            ->method('find')
            ->with($sliderId)
            ->willReturn($expectedSlider);

        $result = $this->sliderService->getSliderById($sliderId);

        $this->assertEquals($expectedSlider, $result);
    }

    public function testCreateSliderSuccess()
    {
        $data = [
            'slider_title' => 'New Slider',
            'slider_identifier' => 'new-slider',
            'slider_description' => 'Description'
        ];

        $createdSlider = new Slider(array_merge($data, ['slider_id' => 1, 'slider_position' => 0]));

        $this->sliderModel->expects($this->once())
            ->method('create')
            ->with(array_merge($data, ['slider_position' => 0]))
            ->willReturn($createdSlider);

        $result = $this->sliderService->createSlider($data);

        $this->assertTrue($result['success']);
        $this->assertEquals('Slider został dodany.', $result['message']);
        $this->assertEquals(1, $result['slider_id']);
    }

    public function testCreateSliderValidationError()
    {
        $data = [
            'slider_title' => '', // Empty title
            'slider_identifier' => '' // Empty identifier
        ];

        $result = $this->sliderService->createSlider($data);

        $this->assertFalse($result['success']);
        $this->assertContains('Tytuł jest wymagany.', $result['errors']);
        $this->assertContains('Identyfikator jest wymagany.', $result['errors']);
    }

    public function testDeleteSliderSuccess()
    {
        $sliderId = 1;

        $this->sliderModel->expects($this->once())
            ->method('where')
            ->with('slider_id', $sliderId)
            ->willReturnSelf();

        $this->sliderModel->expects($this->once())
            ->method('delete')
            ->willReturn(1);

        $result = $this->sliderService->deleteSlider($sliderId);

        $this->assertTrue($result['success']);
        $this->assertEquals('Slider został pomyślnie usunięty.', $result['message']);
    }

    public function testDeleteSliderNotFound()
    {
        $sliderId = 999;

        $this->sliderModel->expects($this->once())
            ->method('where')
            ->with('slider_id', $sliderId)
            ->willReturnSelf();

        $this->sliderModel->expects($this->once())
            ->method('delete')
            ->willReturn(0);

        $result = $this->sliderService->deleteSlider($sliderId);

        $this->assertFalse($result['success']);
        $this->assertEquals('Slider o podanym ID nie istnieje.', $result['error']);
    }

    public function testUpdateSliderInlineSuccess()
    {
        $sliderId = 1;
        $field = 'slider_title';
        $value = 'Updated Title';

        $this->sliderModel->expects($this->once())
            ->method('where')
            ->with('slider_id', $sliderId)
            ->willReturnSelf();

        $this->sliderModel->expects($this->once())
            ->method('update')
            ->with([$field => $value]);

        $result = $this->sliderService->updateSliderInline($sliderId, $field, $value);

        $this->assertTrue($result['success']);
    }
}