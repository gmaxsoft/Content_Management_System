<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controllers\SliderController;
use App\Services\Interfaces\SliderServiceInterface;
use App\Services\Interfaces\SliderFileServiceInterface;
use App\Services\Interfaces\SliderConfigServiceInterface;
use App\Services\TemplateRenderer;
use App\Services\LanguageService;
use App\Services\FileUploadService;
use App\Services\TextUtilities;

class SliderControllerTest extends TestCase
{
    private MockObject $sliderService;
    private MockObject $sliderFileService;
    private MockObject $sliderConfigService;
    private SliderController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks for services
        $this->sliderService = $this->createMock(SliderServiceInterface::class);
        $this->sliderFileService = $this->createMock(SliderFileServiceInterface::class);
        $this->sliderConfigService = $this->createMock(SliderConfigServiceInterface::class);

        // Create real service instances for dependencies
        $templateRenderer = new TemplateRenderer();
        $languageService = new LanguageService();
        $fileUploadService = new FileUploadService();
        $textUtilities = new TextUtilities();

        // Create controller with mocked services
        $this->controller = new SliderController(
            $this->sliderService,
            $this->sliderFileService,
            $this->sliderConfigService,
            $templateRenderer,
            $languageService,
            $fileUploadService,
            $textUtilities
        );
    }

    public function testIndexReturnsConfigData()
    {
        $configData = [
            'slider_width' => 1920,
            'slider_height' => 1080,
            'slider_quality' => 85,
            'slider_format' => 'webp'
        ];

        $this->sliderConfigService->expects($this->once())
            ->method('getConfig')
            ->willReturn($configData);

        // We can't easily test the view rendering without mocking View::renderTemplate
        // but we can verify that the config service is called correctly

        // This would normally call View::renderTemplate, but we'll just ensure no exceptions
        $this->expectNotToPerformAssertions();
        $this->controller->index();
    }

    public function testAddValidDataReturnsSuccess()
    {
        $_POST = [
            'slider_identifier' => 'test-slider',
            'slider_title' => 'Test Slider',
            'slider_description' => 'Test Description'
        ];

        $expectedResult = [
            'success' => true,
            'message' => 'Slider został dodany.',
            'slider_id' => 1
        ];

        $this->sliderService->expects($this->once())
            ->method('createSlider')
            ->with([
                'slider_identifier' => 'test-slider',
                'slider_title' => 'Test Slider',
                'slider_description' => 'Test Description'
            ])
            ->willReturn($expectedResult);

        // Capture output since controller uses echo
        ob_start();
        $this->controller->add();
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertTrue($result['success']);
        $this->assertEquals('Slider został dodany.', $result['message']);
    }

    public function testAddInvalidDataReturnsErrors()
    {
        $_POST = [
            'slider_identifier' => '', // Empty identifier
            'slider_title' => '' // Empty title
        ];

        $expectedResult = [
            'success' => false,
            'errors' => ['Tytuł jest wymagany.', 'Identyfikator jest wymagany.']
        ];

        $this->sliderService->expects($this->once())
            ->method('createSlider')
            ->willReturn($expectedResult);

        ob_start();
        $this->controller->add();
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertFalse($result['success']);
        $this->assertContains('Tytuł jest wymagany.', $result['errors']);
    }

    public function testUpdateCallsServiceWithCorrectData()
    {
        $_POST = [
            'slider_id' => 1,
            'slider_identifier' => 'updated-slider',
            'slider_title' => 'Updated Slider',
            'slider_description' => 'Updated Description'
        ];

        $expectedResult = [
            'success' => true,
            'message' => 'Dane slidera zostały pomyślnie zaktualizowane.'
        ];

        $this->sliderService->expects($this->once())
            ->method('updateSlider')
            ->with(1, [
                'slider_identifier' => 'updated-slider',
                'slider_title' => 'Updated Slider',
                'slider_description' => 'Updated Description'
            ])
            ->willReturn($expectedResult);

        ob_start();
        $this->controller->update();
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertTrue($result['success']);
        $this->assertEquals('Dane slidera zostały pomyślnie zaktualizowane.', $result['message']);
    }

    public function testRemoveCallsServiceWithCorrectId()
    {
        $_POST = ['id' => 5];

        $expectedResult = [
            'success' => true,
            'message' => 'Slider został pomyślnie usunięty.'
        ];

        $this->sliderService->expects($this->once())
            ->method('deleteSlider')
            ->with(5)
            ->willReturn($expectedResult);

        ob_start();
        $this->controller->remove();
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertTrue($result['success']);
        $this->assertEquals('Slider został pomyślnie usunięty.', $result['message']);
    }

    public function testSaveConfigCallsServiceWithCorrectData()
    {
        $_POST = [
            'slider_id' => 1,
            'slider_width' => 1200,
            'slider_height' => 800,
            'slider_quality' => 90,
            'slider_format' => 'jpg'
        ];

        $expectedResult = [
            'success' => true,
            'message' => 'Ustawienia zostały pomyślnie zaktualizowane.'
        ];

        $this->sliderConfigService->expects($this->once())
            ->method('saveConfig')
            ->with([
                'slider_id' => 1,
                'slider_width' => 1200,
                'slider_height' => 800,
                'slider_quality' => 90,
                'slider_format' => 'jpg'
            ])
            ->willReturn($expectedResult);

        ob_start();
        $this->controller->saveConfig();
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertTrue($result['success']);
        $this->assertEquals('Ustawienia zostały pomyślnie zaktualizowane.', $result['message']);
    }
}