<h1 align="center">Laravel 5 Rating System</h1>

<p align="center">:heart: This package helps you to add user based rating system to your model.</p>

<p align="center">
    <a href="https://travis-ci.org/yoeunes/rateable"><img src="https://travis-ci.org/yoeunes/rateable.svg?branch=master" alt="Build Status"></a>
    <a href="https://packagist.org/packages/yoeunes/rateable"><img src="https://poser.pugx.org/yoeunes/rateable/v/stable" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/yoeunes/rateable"><img src="https://poser.pugx.org/yoeunes/rateable/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://scrutinizer-ci.com/g/yoeunes/rateable/build-status/master"><img src="https://scrutinizer-ci.com/g/yoeunes/rateable/badges/build.png?b=master" alt="Build Status"></a>
    <a href="https://scrutinizer-ci.com/g/yoeunes/rateable/?branch=master"><img src="https://scrutinizer-ci.com/g/yoeunes/rateable/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality"></a>
    <a href="https://scrutinizer-ci.com/g/yoeunes/rateable/?branch=master"><img src="https://scrutinizer-ci.com/g/yoeunes/rateable/badges/coverage.png?b=master" alt="Code Coverage"></a>
    <a href="https://packagist.org/packages/yoeunes/rateable"><img src="https://poser.pugx.org/yoeunes/rateable/downloads" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/yoeunes/rateable"><img src="https://poser.pugx.org/yoeunes/rateable/license" alt="License"></a>
</p>

You can install the package using composer

```sh
$ composer require yoeunes/rateable
```

Then add the service provider to `config/app.php`. In Laravel versions 5.5 and beyond, this step can be skipped if package auto-discovery is enabled.

```php
'providers' => [
    ...
    Yoeunes\Rateable\RateableServiceProvider::class
    ...
];
```

Publish the migrations file:

```sh
$ php artisan vendor:publish --provider='Yoeunes\Rateable\RateableServiceProvider' --tag="migrations"
```

As optional if you want to modify the default configuration, you can publish the configuration file:
 
```sh
$ php artisan vendor:publish --provider='Yoeunes\Rateable\RateableServiceProvider' --tag="config"
```

And create tables:

```php
$ php artisan migrate
```

Finally, add feature trait into User model:

```php
<?php

namespace App;

use Yoeunes\Rateable\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use Rateable;
}
```

## Usage


All available APIs are listed below.

#### `Yoeunes\Rateable\Traits\Rateable`

### Create a rating
```php
$user   = User::first();
$lesson = Lesson::first();

$rating = $lesson->getRatingBuilder()
                 ->user($user) // you may also use $user->id
                 ->uniqueRatingForUsers(true) // update if already rated
                 ->rate(3);
```
### Update a rating
```php
$lesson = Lesson::first();

$lesson->updateRating($rating_id, $value); // rating_id and the new rating value
$lesson->updateRatingForUser($user_id, $value); // update all rating for a single user related to the lesson
```

### Delete a rating:
```php
$lesson = Lesson::first();
$lesson->deleteRating($rating_id); // delete a rating with the giving id
$lesson->deleteRatingsForUser($user_id); // delete all rating for a single user related to the lesson
$lesson->resetRating(); // delete all rating related to the lesson
```
### check if a model is already rated:
```php
$lesson->isRated();
$lesson->isRatedBy($user_id);// check if its already rated by the given user
```

### Fetch the average rating:
```php
$lesson->averageRating(); // get the average rating 
$lesson->averageRatingForUser($user_id); // get the average rating for a single user
```

### get list of users who rated a model (raters):
```php
$lesson->raters()->get();
$lesson->raters()->where('name', 'like', '%yoeunes%')->get();
$lesson->raters()->orderBy('name')->get();
```

### get count ratings between by dates
```php
$lesson->countRatingsByDate('2018-02-03 13:23:03', '2018-02-06 15:26:06');
$lesson->countRatingsByDate('2018-02-03 13:23:03');
$lesson->countRatingsByDate(null, '2018-02-06 15:26:06');
$lesson->countRatingsByDate(Carbon::now()->parse('01-04-2017'), Carbon::now()->parse('01-06-2017'));
$lesson->countRatingsByDate(Carbon::now()->subDays(2));
```

### other api methods:
```php
$lesson->countRating()
$lesson->countRatingForUser($user_id)

$lesson->totalRating()
$lesson->totalRatingForUser($user_id)

$lesson->ratingPercentage()
$lesson->positiveRatingCount()
$lesson->positiveRatingTotal()
$lesson->negativeRatingCount()
$lesson->negativeRatingTotal()

Lesson::select('lessons.*')->orderByAverageRating('asc')->get()
Lesson::select('lessons.*')->orderByAverageRating('desc')->get()
```

### Query relations

```php
$ratings = $user->ratings
$ratings = $user->ratings()->where('id', '>', 10)->get()
```

### date transformer

Because we all love having to repeat less, this package allows you to define date transformers. Let's say we are using the following code a lot: $lesson->countRatingsByDate(Carbon::now()->subDays(3)). It can get a little bit annoying and unreadable. Let's solve that!

If you've published the configuration file, you will see something like this:

```php
'date-transformers' => [
    // 'past24hours' => Carbon::now()->subDays(1),
    // 'past7days'   => Carbon::now()->subWeeks(1),
    // 'past14days'  => Carbon::now()->subWeeks(2),
],
```

They are all commented out as default. To make them available, simply uncomment them. The provided ones are serving as an example. You can remove them or add your own ones.

```php
'date-transformers' => [
    'past3days' => Carbon::now()->subDays(3),
],
```

We can now retrieve the rating count like this:

```php
$lesson->countRatingsByDate('past3days');
```



## License

MIT
