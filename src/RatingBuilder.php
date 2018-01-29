<?php

namespace Yoeunes\Rateable;

use Illuminate\Database\Eloquent\Model;
use Yoeunes\Rateable\Exceptions\EmptyUser;
use Yoeunes\Rateable\Exceptions\InvalidRatingValue;
use Yoeunes\Rateable\Exceptions\ModelDoesNotUseRateableTrait;
use Yoeunes\Rateable\Exceptions\RateableModelNotFound;
use Yoeunes\Rateable\Exceptions\UserDoestNotHaveID;
use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\Traits\Rateable;

class RatingBuilder
{
    protected $user;

    protected $rateable;

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

    /**
     * @param int $value
     *
     * @return Rating
     *
     * @throws \Throwable
     */
    public function rate(int $value)
    {
        $rating = new Rating();

        throw_if($value< config('rateable.min') || $value > config('rateable.max'), InvalidRatingValue::class, 'Invalid rating value');
        $rating->value  = $value;

        throw_if(empty($this->user), EmptyUser::class, 'Empty user');
        $rating->user_id = $this->user;

        throw_if(empty($this->rateable->id), RateableModelNotFound::class, 'Rateable model not found');
        $rating->rateable_type = get_class($this->rateable);
        $rating->rateable_id = $this->rateable->id;

        $rating->save();

        return $rating;
    }
}
