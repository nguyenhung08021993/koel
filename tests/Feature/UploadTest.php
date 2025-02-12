<?php

namespace Tests\Feature;

use App\Exceptions\MediaPathNotSetException;
use App\Exceptions\SongUploadFailedException;
use App\Models\Setting;
use App\Models\Song;
use App\Models\User;
use App\Services\UploadService;
use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;

class UploadTest extends TestCase
{
    private UploadService|MockInterface $uploadService;

    public function setUp(): void
    {
        parent::setUp();

        $this->uploadService = self::mock(UploadService::class);
    }

    public function testUnauthorizedPost(): void
    {
        Setting::set('media_path', '/media/koel');
        $file = UploadedFile::fake()->create('foo.mp3', 2048);

        $this->uploadService
            ->shouldReceive('handleUploadedFile')
            ->never();

        $this->postAs('/api/upload', ['file' => $file])->assertForbidden();
    }

    /** @return array<mixed> */
    public function provideUploadExceptions(): array
    {
        return [
            [MediaPathNotSetException::class, 403],
            [SongUploadFailedException::class, 400],
        ];
    }

    /** @dataProvider provideUploadExceptions */
    public function testPostShouldFail(string $exceptionClass, int $statusCode): void
    {
        $file = UploadedFile::fake()->create('foo.mp3', 2048);

        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $this->uploadService
            ->shouldReceive('handleUploadedFile')
            ->once()
            ->with($file)
            ->andThrow($exceptionClass);

        $this->postAs('/api/upload', ['file' => $file], $admin)->assertStatus($statusCode);
    }

    public function testPost(): void
    {
        Setting::set('media_path', '/media/koel');
        $file = UploadedFile::fake()->create('foo.mp3', 2048);

        /** @var Song $song */
        $song = Song::factory()->create();

        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $this->uploadService
            ->shouldReceive('handleUploadedFile')
            ->once()
            ->with($file)
            ->andReturn($song);

        $this->postAs('/api/upload', ['file' => $file], $admin)->assertJsonStructure(['song', 'album']);
    }
}
