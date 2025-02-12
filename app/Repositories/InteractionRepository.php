<?php

namespace App\Repositories;

use App\Models\Interaction;
use App\Models\User;
use App\Repositories\Traits\ByCurrentUser;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class InteractionRepository extends Repository
{
    use ByCurrentUser;

    /** @return Collection|array<Interaction> */
    public function getUserFavorites(User $user): Collection
    {
        return $this->model->where([
            'user_id' => $user->id,
            'liked' => true,
        ])
            ->with('song')
            ->pluck('song');
    }

    /** @return array<Interaction> */
    public function getRecentlyPlayed(User $user, ?int $count = null): array
    {
        /** @var Builder $query */
        $query = $this->model
            ->where('user_id', $user->id)
            ->where('play_count', '>', 0)
            ->orderBy('updated_at', 'DESC');

        if ($count) {
            $query = $query->take($count);
        }

        return $query->pluck('song_id')->all();
    }
}
