<?php

namespace Yoeunes\Rateable\Tests;

use Laracasts\TestDummy\Factory;
use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\Tests\Stubs\Models\User;
use Yoeunes\Rateable\Tests\Stubs\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RateableTest extends TestCase
{
    /** @test */
    public function it_test_if_rateable_is_a_morph_to_relation()
    {
        /** @var Rating $rating */
        $rating = Factory::create(Rating::class);
        $this->assertInstanceOf(MorphTo::class, $rating->rateable());
    }

    /** @test */
    public function it_test_if_user_is_a_belongs_to_relation()
    {
        /** @var Rating $rating */
        $rating = Factory::create(Rating::class);
        $this->assertInstanceOf(BelongsTo::class, $rating->user());
    }

    /** @test */
    public function it_test_if_ratings_is_a_morph_many_relation()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);
        $this->assertInstanceOf(MorphMany::class, $lesson->ratings());
    }

    /** @test */
    public function it_return_ratings_count()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        Factory::times(3)->create(Rating::class, ['rateable_id' => $lesson->id]);

        $this->assertEquals(3, $lesson->ratings()->count());
    }

    /** @test */
    public function it_return_average_rating_for_5_system_stars()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 1]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 2]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 3]);

        $this->assertEquals(2, $lesson->averageRating());
    }

    /** @test */
    public function it_return_percent_rating_for_5_system_stars()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 1]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 2]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 3]);

        $this->assertEquals(40, $lesson->ratingPercentage());
    }

    /** @test */
    public function it_rate_lesson_using_rating_builder()
    {
        /** @var Lesson */
        $lesson = Factory::create(Lesson::class);

        /** @var User $user */
        $user = Factory::create(User::class);

        $rating = $lesson
            ->getRatingBuilder()
            ->user($user)
            ->rate(3);

        $this->assertEquals(3, $lesson->averageRating());
        $this->assertEquals($rating->value, $lesson->averageRating());
    }

    /** @test */
    public function it_test_if_a_lesson_is_already_rated()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id]);
        $this->assertTrue($lesson->isRated());
    }

    /** @test */
    public function it_test_if_a_lesson_is_not_already_rated()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        $this->assertFalse($lesson->isRated());
    }

    /** @test */
    public function it_test_if_a_lesson_is_already_rated_by_a_user()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        /** @var User $user */
        $user = Factory::create(User::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'user_id' => $user->id]);
        $this->assertTrue($lesson->isRatedBy($user->id));
    }

    /** @test */
    public function it_test_if_a_lesson_is_not_already_rated_by_a_user()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id]);
        $this->assertFalse($lesson->isRatedBy(3));
    }
}
