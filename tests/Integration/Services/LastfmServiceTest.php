<?php

namespace Tests\Integration\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Services\LastfmService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Log\Logger;
use Mockery;
use Tests\TestCase;

class LastfmServiceTest extends TestCase
{
    public function testGetArtistInformation(): void
    {
        /** @var Artist $artist */
        $artist = Artist::factory()->make(['name' => 'foo']);

        /** @var Client $client */
        $client = Mockery::mock(Client::class, [
            'get' => new Response(200, [], file_get_contents(__DIR__ . '../../../blobs/lastfm/artist.json')),
        ]);

        $api = new LastfmService($client, app(Cache::class), app(Logger::class));
        $info = $api->getArtistInformation($artist);

        self::assertEquals([
            'url' => 'https://www.last.fm/music/Kamelot',
            'image' => null,
            'bio' => [
                'summary' => 'Quisque ut nisi.',
                'full' => 'Quisque ut nisi. Vestibulum ullamcorper mauris at ligula.',
            ],
        ], $info->toArray());

        self::assertNotNull(cache()->get('0aff3bc1259154f0e9db860026cda7a6'));
    }

    public function testGetArtistInformationForNonExistentArtist(): void
    {
        /** @var Artist $artist */
        $artist = Artist::factory()->make();

        /** @var Client $client */
        $client = Mockery::mock(Client::class, [
            'get' => new Response(400, [], file_get_contents(__DIR__ . '../../../blobs/lastfm/artist-notfound.json')),
        ]);

        $api = new LastfmService($client, app(Cache::class), app(Logger::class));

        self::assertNull($api->getArtistInformation($artist));
    }

    public function testGetAlbumInformation(): void
    {
        /** @var Artist $artist */
        $artist = Artist::factory()->create(['name' => 'bar']);

        /** @var Album $album */
        $album = Album::factory()->for($artist)->create(['name' => 'foo']);

        /** @var Client $client */
        $client = Mockery::mock(Client::class, [
            'get' => new Response(200, [], file_get_contents(__DIR__ . '../../../blobs/lastfm/album.json')),
        ]);

        $api = new LastfmService($client, app(Cache::class), app(Logger::class));
        $info = $api->getAlbumInformation($album);

        self::assertEquals([
            'url' => 'https://www.last.fm/music/Kamelot/Epica',
            'cover' => null,
            'tracks' => [
                [
                    'title' => 'Track 1',
                    'url' => 'https://foo/track1',
                    'length' => 100,
                ],
                [
                    'title' => 'Track 2',
                    'url' => 'https://foo/track2',
                    'length' => 150,
                ],
            ],
            'wiki' => [
                'summary' => 'Quisque ut nisi.',
                'full' => 'Quisque ut nisi. Vestibulum ullamcorper mauris at ligula.',
            ],
        ], $info->toArray());

        self::assertNotNull(cache()->get('fca889d13b3222589d7d020669cc5a38'));
    }

    public function testGetAlbumInformationForNonExistentAlbum(): void
    {
        /** @var Album $album */
        $album = Album::factory()->create();

        /** @var Client $client */
        $client = Mockery::mock(Client::class, [
            'get' => new Response(400, [], file_get_contents(__DIR__ . '../../../blobs/lastfm/album-notfound.json')),
        ]);

        $api = new LastfmService($client, app(Cache::class), app(Logger::class));

        self::assertNull($api->getAlbumInformation($album));
    }
}
