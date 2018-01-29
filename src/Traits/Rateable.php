<?php

namespace Yoeunes\Rateable\Traits;

use Illuminate\Support\Facades\DB;
use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\RatingBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

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

    public function countRating()
    {
        return $this->ratings()->count();
    }

    public function totalRating()
    {
        return $this->ratings()->sum('value');
    }

    public function averageRatingForUser(int $user_id)
    {
        return $this->ratings()->where('user_id', $user_id)->avg('value');
    }

    public function totalRatingForUser(int $user_id)
    {
        return $this->ratings()->where('user_id', $user_id)->sum('value');
    }

    public function countRatingForUser(int $user_id)
    {
        return $this->ratings()->where('user_id', $user_id)->count();
    }

    public function ratingPercentage()
    {
        $max = config('rateable.max_rating');

        $quantity = $this->ratings()->count();

        $total = $this->totalRating();

        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    public function positiveRatingCount()
    {
        return $this->ratings()->where('value', '>=', '0')->count();
    }

    public function positiveRatingTotal()
    {
        return $this->ratings()->where('value', '>=', '0')->sum('value');
    }

    public function negativeRatingCount()
    {
        return $this->ratings()->where('value', '<', '0')->count();
    }

    public function negativeRatingTotal()
    {
        return $this->ratings()->where('value', '<', '0')->sum('value');
    }

    public function isRated()
    {
        return $this->ratings()->exists();
    }

    public function isRatedBy(int $user_id)
    {
        return $this->ratings()->where('user_id', $user_id)->exists();
    }

    /**
     * to order by average_rating.
     *
     * add protected $appends = [ 'average_rating' ]; to your model
     *
     * Lesson::all()->sortBy('average_rating')
     * Lesson::with('relatedModel')->get()->sortBy('average_rating')
     * Lesson::where('status', 'published')->get()->sortBy('average_rating')
     *
     * @return mixed
     */
    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    public function scopeOrderByAverageRating(Builder $query, string $direction = 'asc')
    {
        $morph_map = config('rateable.morph_map');

        $class = get_class($this);

        $rateable_type = array_key_exists($class, $morph_map) ? $morph_map[$class] : $class;

        return $query
            ->leftJoin('ratings', function (JoinClause $join) use ($rateable_type) {
                $join
                    ->on('ratings.rateable_id', $this->getTable() . '.id')
                    ->where('ratings.rateable_type', $rateable_type);
            })
            ->addSelect(DB::raw('AVG(ratings.value) as average_rating'))
            ->groupBy($this->getTable(). '.id')
            ->orderBy('average_rating', $direction);
    }

    public function deleteRating(int $rating_id)
    {
        return $this->ratings()->where('id', $rating_id)->delete();
    }

    public function resetRating()
    {
        return $this->ratings()->delete();
    }

    public function deleteRatingsForUser(int $user_id)
    {
        return $this->ratings()->where('user_id', $user_id)->delete();
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
