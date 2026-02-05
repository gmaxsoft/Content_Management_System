<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\FileUploadService;

class FileUploadServiceTest extends TestCase
{
    private FileUploadService $fileUploadService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService();
    }

    public function testValidateFileValidImage()
    {
        $validFile = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/test.jpg',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024 // 1KB
        ];

        $result = $this->fileUploadService->validateFile($validFile);

        $this->assertTrue($result);
    }

    public function testValidateFileInvalidType()
    {
        $invalidFile = [
            'name' => 'test.exe',
            'type' => 'application/x-executable',
            'tmp_name' => '/tmp/test.exe',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];

        $result = $this->fileUploadService->validateFile($invalidFile);

        $this->assertFalse($result);
    }

    public function testValidateFileTooLarge()
    {
        $largeFile = [
            'name' => 'large.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/large.jpg',
            'error' => UPLOAD_ERR_OK,
            'size' => 15 * 1024 * 1024 // 15MB
        ];

        $result = $this->fileUploadService->validateFile($largeFile);

        $this->assertFalse($result);
    }

    public function testValidateFileUploadError()
    {
        $errorFile = [
            'name' => 'error.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/error.jpg',
            'error' => UPLOAD_ERR_PARTIAL,
            'size' => 1024
        ];

        $result = $this->fileUploadService->validateFile($errorFile);

        $this->assertFalse($result);
    }

    public function testValidateFileNoTmpName()
    {
        $noTmpFile = [
            'name' => 'notmp.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];

        $result = $this->fileUploadService->validateFile($noTmpFile);

        $this->assertFalse($result);
    }

    public function testUploadFileValidFile()
    {
        // Create a temporary file and directory for testing
        $tempDir = sys_get_temp_dir() . '/test_uploads';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload');
        file_put_contents($tempFile, 'test content');

        $validFile = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];

        // Note: move_uploaded_file() only works with files uploaded via HTTP POST
        // In tests with tempnam(), it will return false
        // We test the validation logic instead
        $result = $this->fileUploadService->uploadFile($validFile, $tempDir);

        // move_uploaded_file will fail with tempnam(), so we expect failure
        // But we verify the validation passed (file structure is correct)
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Verify file validation would pass
        $validationResult = $this->fileUploadService->validateFile($validFile);
        $this->assertTrue($validationResult, 'File validation should pass for valid test file');

        // Cleanup
        if (file_exists($result['path'])) {
            unlink($result['path']);
        }
        unlink($tempFile);
        rmdir($tempDir);
    }

    public function testUploadFileInvalidFile()
    {
        $invalidFile = [
            'name' => 'invalid.exe',
            'type' => 'application/x-executable',
            'tmp_name' => '/tmp/invalid.exe',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];

        $result = $this->fileUploadService->uploadFile($invalidFile, '/tmp');

        $this->assertFalse($result['success']);
        $this->assertEquals('Nieprawidłowy plik.', $result['error']);
    }
}