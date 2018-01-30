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
$ composer require yoeunes/rateable -vvv
```

Then add the service provider to `config/app.php`

```php
Yoeunes\Rateable\RateableServiceProvider::class
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

```php
$lesson->averageRating()
$lesson->averageRatingForUser($user_id)

$lesson->countRating()
$lesson->countRatingForUser($user_id)

$lesson->totalRating()
$lesson->totalRatingForUser($user_id)

$lesson->ratingPercentage()
$lesson->positiveRatingCount()
$lesson->positiveRatingTotal()
$lesson->negativeRatingCount()
$lesson->negativeRatingTotal()

$lesson->isRated()
$lesson->isRatedBy($user_id)

$lesson->deleteRating($rating_id)
$lesson->resetRating()
$lesson->deleteRatingsForUser($user_id)
$lesson->updateRatingForUser($user_id, $value)
$lesson->updateRating($rating_id, $value)


Lesson::select('lessons.*')->orderByAverageRating('asc')->get()
Lesson::select('lessons.*')->orderByAverageRating('desc')->get()

$lesson
    ->getRatingBuilder()
    ->user($user)
    ->rate(3);

```

### Query relations

```php
$ratings = $user->ratings
$ratings = $user->ratings()->where('id', '>', 10)->get()
$ratings = $user->ratings()->orderByDesc('id')->get()
```

## License

MIT
