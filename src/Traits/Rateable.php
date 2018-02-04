<?php

namespace Yoeunes\Rateable\Traits;

use Illuminate\Support\Facades\DB;
use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\RatingBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Yoeunes\Rateable\Exceptions\InvalidRatingValue;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        return $query
            ->leftJoin('ratings', function (JoinClause $join) {
                $join
                    ->on('ratings.rateable_id', $this->getTable() . '.id')
                    ->where('ratings.rateable_type', Relation::getMorphedModel(__CLASS__) ?? __CLASS__);
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
     * @param int $user_id
     * @param int $value
     *
     * @return int
     *
     * @throws \Throwable
     */
    public function updateRatingForUser(int $user_id, int $value)
    {
        throw_if($value < config('rateable.min_rating') || $value > config('rateable.max_rating'), InvalidRatingValue::class, 'Invalid rating value');

        return $this->ratings()->where('user_id', $user_id)->update(['value' => $value]);
    }

    /**
     * @param int $rating_id
     * @param int $value
     *
     * @return int
     *
     * @throws \Throwable
     */
    public function updateRating(int $rating_id, int $value)
    {
        throw_if($value < config('rateable.min_rating') || $value > config('rateable.max_rating'), InvalidRatingValue::class, 'Invalid rating value');

        return $this->ratings()->where('id', $rating_id)->update(['value' => $value]);
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

    public function raters()
    {
        return $this->morphToMany(config('rateable.user'), 'rateable', 'ratings');
    }

    public function countRatingsByDate($from = null, $to = null)
    {
        $query = $this->ratings();

        if (! empty($from) && empty($to)) {
            $query->where('created_at', '>=', date_transformer($from));
        } elseif (empty($from) && ! empty($to)) {
            $query->where('created_at', '<=', date_transformer($to));
        } elseif (! empty($from) && ! empty($to)) {
            $query->whereBetween('created_at', [date_transformer($from), date_transformer($to)]);
        }

        return $query->sum('value');
    }
}
