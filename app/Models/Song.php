<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

/**
 * @property string $path
 * @property string $title
 * @property Album $album
 * @property Artist $artist
 * @property float $length
 * @property string $lyrics
 * @property int $track
 * @property int $disc
 * @property int $album_id
 * @property string $id
 * @property int $artist_id
 * @property int $mtime
 * @property ?bool $liked Whether the song is liked by the current user (dynamically calculated)
 * @property ?int $play_count The number of times the song has been played by the current user (dynamically calculated)
 * @property Carbon $created_at
 *
 * @method static self updateOrCreate(array $where, array $params)
 * @method static Builder select(string $string)
 * @method static Builder inDirectory(string $path)
 * @method static self first()
 * @method static Builder orderBy(...$args)
 * @method static int count()
 * @method static self|Collection|null find($id)
 * @method static Builder take(int $count)
 * @method static float|int sum(string $column)
 * @method static Builder latest(string $column = 'created_at')
 * @method static Builder where(...$params)
 * @method static Song findOrFail(string $id)
 */
class Song extends Model
{
    use HasFactory;
    use Searchable;
    use SupportsDeleteWhereValueNotIn;
    use SupportsS3;

    public const ID_REGEX = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    public $incrementing = false;
    protected $guarded = [];

    protected $hidden = ['updated_at', 'path', 'mtime'];

    protected $casts = [
        'length' => 'float',
        'mtime' => 'int',
        'track' => 'int',
        'disc' => 'int',
    ];

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(static fn (self $song) => $song->id = Str::uuid()->toString());
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    /**
     * Scope a query to only include songs in a given directory.
     */
    public function scopeInDirectory(Builder $query, string $path): Builder
    {
        // Make sure the path ends with a directory separator.
        $path = rtrim(trim($path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $query->where('path', 'LIKE', "$path%");
    }

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn (?string $value) => $value ?: pathinfo($this->path, PATHINFO_FILENAME),
            set: static fn (string $value) => html_entity_decode($value)
        );
    }

    protected function lyrics(): Attribute
    {
        // Since we're displaying the lyrics using <pre>, replace breaks with newlines and strip all tags.
        $normalizer = static fn (?string $value): string => strip_tags(preg_replace('#<br\s*/?>#i', PHP_EOL, $value));

        return new Attribute(get: $normalizer, set: $normalizer);
    }

    public static function withMeta(User $scopedUser, ?Builder $query = null): Builder
    {
        $query ??= static::query();

        return $query
            ->with('artist', 'album', 'album.artist')
            ->leftJoin('interactions', static function (JoinClause $join) use ($scopedUser): void {
                $join->on('interactions.song_id', '=', 'songs.id')
                    ->where('interactions.user_id', $scopedUser->id);
            })
            ->join('albums', 'songs.album_id', '=', 'albums.id')
            ->join('artists', 'songs.artist_id', '=', 'artists.id')
            ->select(
                'songs.*',
                'albums.name',
                'artists.name',
                'interactions.liked',
                'interactions.play_count'
            );
    }

    public function scopeWithMeta(Builder $query, User $scopedUser): Builder
    {
        return static::withMeta($scopedUser, $query);
    }

    /** @return array<mixed> */
    public function toSearchableArray(): array
    {
        $array = [
            'id' => $this->id,
            'title' => $this->title,
        ];

        if (!$this->artist->is_unknown && !$this->artist->is_various) {
            $array['artist'] = $this->artist->name;
        }

        return $array;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
