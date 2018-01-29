<?php

namespace Yoeunes\Rateable\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Yoeunes\Rateable\RateableServiceProvider;
use Yoeunes\Rateable\Tests\Stubs\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            RateableServiceProvider::class,
        ];
    }

    public function tearDown()
    {
        Schema::drop('ratings');
        Schema::drop('lessons');
        Schema::drop('users');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app', [
            'name'   => 'Larafast',
            'locale' => 'en',
            'key'    => 'base64:O30Ogm4MaKjrqSAXq5okDox31Yt3MRn6eUjKymabybw=',
            'cipher' => 'AES-256-CBC',
        ]);

        $app['config']->set('auth', [
            'defaults' => [
                'guard'     => 'web',
                'passwords' => 'users',
            ],
            'guards' => [
                'web' => [
                    'driver'   => 'session',
                    'provider' => 'users',
                ],

                'api' => [
                    'driver'   => 'token',
                    'provider' => 'users',
                ],
            ],
            'providers'    => [
                'users' => [
                    'driver' => 'eloquent',
                    'model'  => User::class,
                ],
            ],
        ]);

        $app['config']->set('rating', [
            'user'       => User::class,
            'min_rating' => 0,
            'max_rating' => 5,
        ]);

        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('subject');

            $table->timestamps();
        });

        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('value');
            $table->morphs('rateable');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->rememberToken();

            $table->timestamps();
        });
    }
}
