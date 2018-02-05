<?php

namespace Yoeunes\Rateable\Tests;

use Laracasts\TestDummy\Factory;
use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\Tests\Stubs\Models\User;
use Yoeunes\Rateable\Tests\Stubs\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
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
    public function it_return_average_rating_for_5_system_stars()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        /** @var User $user */
        $user = Factory::create(User::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 1, 'user_id' => $user->id]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 2, 'user_id' => $user->id]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 3]);

        $this->assertEquals(2, $lesson->averageRating());
        $this->assertEquals(1.5, $lesson->averageRatingForUser($user->id));
    }

    /** @test */
    public function it_return_total_rating_for_5_system_stars()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        /** @var User $user */
        $user = Factory::create(User::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 1, 'user_id' => $user->id]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 2, 'user_id' => $user->id]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 3]);

        $this->assertEquals(6, $lesson->totalRating());
        $this->assertEquals(3, $lesson->totalRatingForUser($user->id));
    }

    /** @test */
    public function it_return_count_rating_for_5_system_stars()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        /** @var User $user */
        $user = Factory::create(User::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 1, 'user_id' => $user->id]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 2, 'user_id' => $user->id]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 3]);

        $this->assertEquals(3, $lesson->countRating());
        $this->assertEquals(2, $lesson->countRatingForUser($user->id));
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

    /** @test */
    public function it_order_lessons_by_most_rated()
    {
        $lessons = Factory::times(3)->create(Lesson::class);

        Factory::create(Rating::class, ['rateable_id' => $lessons[0]->id, 'value' => 5]);

        Factory::create(Rating::class, ['rateable_id' => $lessons[1]->id, 'value' => 3]);
        Factory::create(Rating::class, ['rateable_id' => $lessons[1]->id, 'value' => 1]);

        Factory::create(Rating::class, ['rateable_id' => $lessons[2]->id, 'value' => 4]);

        $sortedLessons = Lesson::select('lessons.*')->orderByAverageRating()->get();

        $this->assertEquals(3, $sortedLessons->count());
        $this->assertGreaterThan($sortedLessons[0]->averageRating(), $sortedLessons[1]->averageRating());
        $this->assertGreaterThan($sortedLessons[1]->averageRating(), $sortedLessons[2]->averageRating());
    }

    /** @test */
    public function it_delete_rating_by_id()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        $ratings = Factory::times(3)->create(Rating::class, ['rateable_id' => $lesson->id]);

        $lesson->deleteRating($ratings[1]->id);
        $this->assertDatabaseMissing('ratings', ['id' => $ratings[1]->id]);
    }

    /** @test */
    public function it_reset_rating_for_a_lesson()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        Factory::times(3)->create(Rating::class, ['rateable_id' => $lesson->id]);

        $lesson->resetRating();
        $this->assertEquals(0, $lesson->averageRating());
    }

    /** @test */
    public function it_delete_rating_for_a_user()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        /** @var Lesson $lesson */
        $user = Factory::create(User::class);

        Factory::times(3)->create(Rating::class, ['rateable_id' => $lesson->id, 'user_id' => $user->id]);

        $lesson->deleteRatingsForUser($user->id);

        $this->assertEquals(0, $lesson->averageRatingForUser($user->id));
    }

    /** @test */
    public function it_get_raters_for_a_specific_lesson()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        $users = Factory::times(2)->create(User::class);

        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'user_id' => $users[0]->id]);
        Factory::create(Rating::class, ['rateable_id' => $lesson->id, 'user_id' => $users[1]->id]);
        Factory::times(3)->create(Rating::class, ['rateable_id' => $lesson->id]);
        Factory::times(4)->create(Rating::class);

        $this->assertCount(5, $lesson->ratings()->get());
    }

    /** @test */
    public function it_get_votes_from_start_date()
    {
        /** @var Lesson $lesson */
        $lesson = Factory::create(Lesson::class);

        $votes = Factory::times(5)->create(Rating::class, ['rateable_id' => $lesson->id, 'value' => 1]);

        $votes[0]->created_at = '2018-02-01 11:21:01';
        $votes[0]->save();

        $votes[1]->created_at = '2018-02-02 12:22:02';
        $votes[1]->save();

        $votes[2]->created_at = '2018-02-03 13:23:03';
        $votes[2]->save();

        $votes[3]->created_at = '2018-02-04 14:24:04';
        $votes[3]->save();

        $votes[4]->created_at = '2018-02-05 15:25:05';
        $votes[4]->save();

        $this->assertEquals(5, $lesson->countRatingsByDate());
        $this->assertEquals(3, $lesson->countRatingsByDate('2018-02-03 13:23:03'));
        $this->assertEquals(0, $lesson->countRatingsByDate('2018-02-06 15:26:06'));
        $this->assertEquals(2, $lesson->countRatingsByDate('2018-02-03 13:23:03', '2018-02-04 14:24:04'));
        $this->assertEquals(2, $lesson->countRatingsByDate(null, '2018-02-02 12:22:02'));
    }

    /** @test */
    public function it_order_lessons_by_most_rated_with_a_morph_map()
    {
        Relation::morphMap(['lessons' => Lesson::class]);

        $lessons = Factory::times(3)->create(Lesson::class);

        Factory::create(Rating::class, ['rateable_id' => $lessons[0]->id, 'rateable_type' => 'lessons', 'value' => 5]);

        Factory::create(Rating::class, ['rateable_id' => $lessons[1]->id, 'rateable_type' => 'lessons', 'value' => 3]);
        Factory::create(Rating::class, ['rateable_id' => $lessons[1]->id, 'rateable_type' => 'lessons', 'value' => 1]);

        Factory::create(Rating::class, ['rateable_id' => $lessons[2]->id, 'rateable_type' => 'lessons', 'value' => 4]);

        $sortedLessons = Lesson::with('ratings')->select('lessons.*')->orderByAverageRating()->get();

        $this->assertEquals($lessons[1]->id, $sortedLessons[0]->id);
        $this->assertEquals($lessons[2]->id, $sortedLessons[1]->id);
        $this->assertEquals($lessons[0]->id, $sortedLessons[2]->id);
    }
}
