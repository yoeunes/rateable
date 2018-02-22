<?php

namespace Yoeunes\Rateable\Builders;

use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;
use Yoeunes\Rateable\Exceptions\EmptyUser;
use Yoeunes\Rateable\Exceptions\InvalidRatingValue;
use Yoeunes\Rateable\Exceptions\UserDoestNotHaveID;
use Yoeunes\Rateable\Exceptions\RateableModelNotFound;
use Yoeunes\Rateable\Exceptions\ModelDoesNotUseRateableTrait;

class RatingBuilder
{
    protected $user;

    protected $rateable;

    protected $uniqueRatingForUsers = true;

    public function __construct()
    {
        if (config('rateable.auth_user')) {
            $this->user = auth()->id();
        }

        if (config('rateable.user_rate_once')) {
            $this->uniqueVoteForUsers = true;
        }
    }

    /**
     * @param Model|int $user
     *
     * @return RatingBuilder
     *
     * @throws \Throwable
     */
    public function user($user)
    {
        throw_if($user instanceof Model && empty($user->id), UserDoestNotHaveID::class, 'User object does not have ID');

        $this->user = $user instanceof Model ? $user->id : $user;

        return $this;
    }

    /**
     * @param Model $rateable
     *
     * @return $this
     *
     * @throws \Throwable
     */
    public function rateable(Model $rateable)
    {
        throw_unless(in_array(Rateable::class, class_uses_recursive($rateable)), ModelDoesNotUseRateableTrait::class, get_class($rateable) . ' does not use the Rateable Trait');

        $this->rateable = $rateable;

        return $this;
    }

    public function uniqueRatingForUsers(bool $unique)
    {
        $this->uniqueRatingForUsers = $unique;

        return $this;
    }

    /**
     * @param int $value
     *
     * @return Rating
     *
     * @throws \Throwable
     */
    public function rate(int $value)
    {
        throw_if($value < config('rateable.min_rating') || $value > config('rateable.max_rating'), InvalidRatingValue::class, 'Invalid rating value');

        throw_if(empty($this->user), EmptyUser::class, 'Empty user');

        throw_if(empty($this->rateable->id), RateableModelNotFound::class, 'Rateable model not found');

        $data = [
            'user_id'       => $this->user,
            'rateable_id'   => $this->rateable->id,
            'rateable_type' => morph_type($this->rateable),
        ];

        $ratingModel = config('rateable.rating');

        $rating = $this->uniqueRatingForUsers ? (new $ratingModel)->firstOrNew($data) : (new $ratingModel)->fill($data);

        $rating->value = $value;

        $rating->save();

        return $rating;
    }
}
