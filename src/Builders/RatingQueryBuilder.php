<?php

namespace Yoeunes\Rateable\Builders;

use Yoeunes\Rateable\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Yoeunes\Rateable\Exceptions\UserDoestNotHaveID;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Yoeunes\Rateable\Exceptions\ModelDoesNotUseRateableTrait;

class RatingQueryBuilder
{
    protected $query = null;

    public function __construct(MorphMany $query)
    {
        $this->query = $query;
    }

    public function from($date)
    {
        $this->query = $this->query->where('created_at', '>=', date_transformer($date));

        return $this;
    }

    public function to($date)
    {
        $this->query = $this->query->where('created_at', '<=', date_transformer($date));

        return $this;
    }

    /**
     * @param $user
     *
     * @return RatingQueryBuilder
     *
     * @throws \Throwable
     */
    public function user($user)
    {
        throw_if($user instanceof Model && empty($user->id), UserDoestNotHaveID::class, 'User object does not have ID');

        $this->query = $this->query->where('user_id', $user instanceof Model ? $user->id : $user);

        return $this;
    }

    /**
     * @param Model $rateable
     *
     * @return RatingQueryBuilder
     *
     * @throws \Throwable
     */
    public function rateable(Model $rateable)
    {
        throw_unless(in_array(Rateable::class, class_uses_recursive($rateable)), ModelDoesNotUseRateableTrait::class, get_class($rateable).' does not use the Rateable Trait');

        $this->query = $this->query
            ->leftJoin('ratings', function (JoinClause $join) use ($rateable) {
                $join
                    ->on('ratings.rateable_id', $rateable->getTable() . '.id')
                    ->where('ratings.rateable_type', morph_type($rateable));
            });

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
