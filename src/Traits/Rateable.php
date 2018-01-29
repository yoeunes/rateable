<?php

namespace Yoeunes\Rateable\Traits;

use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\RatingBuilder;

trait Rateable
{
    /**
     * This model has many ratings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('value');
    }

    public function sumRating()
    {
        return $this->ratings()->sum('value');
    }

    public function userAverageRating(int $user_id)
    {
        return $this->ratings()->where('user_id', $user_id)->avg('value');
    }

    public function userSumRating(int $user_id)
    {
        return $this->ratings()->where('user_id', $user_id)->sum('value');
    }

    public function ratingPercent(int $max = null)
    {
        $max = $max ?? config('rateable.max_rating');

        $quantity = $this->ratings()->count();

        $total = $this->sumRating();

        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    public function positiveRatingCount()
    {
        return $this->ratings()->where('value', '>=', '0')->count();
    }

    public function negativeRatingCount()
    {
        return $this->ratings()->where('value', '<', '0')->count();
    }

    /**
     * @return RatingBuilder
     *
     * @throws \Throwable
     */
    public function getRatingBuilder()
    {
        return (new RatingBuilder())
            ->rateable($this);
    }
}
