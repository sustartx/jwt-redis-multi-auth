<?php

return [
    // default (= 'bcrypt'), 'jwtredismultiauth', 'new hashker class path'
    'hasher' => 'default',

    "register_middlewares" => true,

    /*
    |--------------------------------------------------------------------------
    | JWTRedis User Model Observer
    |--------------------------------------------------------------------------
    |
    | This observer class, listening all events on your user model. Is triggered
    | when you assign roles & permissions to user, or update and delete to
    | your user model.
    |
    */
    'observer' => \SuStartX\JWTRedisMultiAuth\Observers\AuthAuthenticatableModelRedisObserver::class,

    /*
    |--------------------------------------------------------------------------
    | Observer Events Are Queued
    |--------------------------------------------------------------------------
    |
    | If this option is true, model's events are processed as a job on queue.
    | The job will be executed after the database transactions are commit.
    |
    | * ~ Don't forget to run Queue Worker if this option is true. ~ *
    |
    */
    'observer_events_queue' => env('JWTREDIS_OBSERVER_EVENTS_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Store on Redis up to jwt_ttl value.
    |--------------------------------------------------------------------------
    |
    | If it's option is true, user stored in Redis up to jwt_ttl value time.
    |
    */
    'redis_ttl_jwt' => true,

    /*
    |--------------------------------------------------------------------------
    | Store on Redis up to specific time
    |--------------------------------------------------------------------------
    |
    |  User stored in Redis redis_ttl value time.
    |
    */
    'redis_ttl' => env('JWTREDIS_REDIS_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Cache Prefix
    |--------------------------------------------------------------------------
    |
    | If it's user id is 1, this user stored in Redis as auth_1.
    |
    */
    'redis_auth_prefix' => env('JWTREDIS_REDIS_AUTH_PREFIX', 'auth_'),

    /*
    |--------------------------------------------------------------------------
    | Igbinary Serialization
    |--------------------------------------------------------------------------
    |
    | Igbinary Serialization provides a better performance and lower memory
    | usage than PHP Serialization.
    |
    | * ~ Don't forget to enable igbinary extension if this option is true. ~ *
    |
    */
    'igbinary_serialization' => env('JWTREDIS_IGBINARY_SERIALIZATION', false),

    /*
    |--------------------------------------------------------------------------
    | Cache This Relations When User Has Authenticated
    |--------------------------------------------------------------------------
    |
    | You can add this array to your own relations, anything you want to store
    | in Redis. We recommend caching only roles and permissions here as much as
    | possible.
    |
    */
    'cache_relations' => [
        'roles.permissions',
        'permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Customize All Exception Messages and Codes
    |--------------------------------------------------------------------------
    |
    | You can customize error code,message,title for your application.
    |
    */
    'errors' => [
        'default' => [
            'title'   => 'Operation Failed',
            'message' => 'An error occurred.',
            'code'    => 0,
        ],

        'JWTException' => [
            'title'   => 'Operation Failed',
            'message' => 'A token is required',
            'code'    => 3,
        ],

        'TokenBlacklistedException' => [
            'title'   => 'Operation Failed',
            'message' => 'The token has been blacklisted.',
            'code'    => 4,
        ],

        'TokenExpiredException' => [
            'title'   => 'Operation Failed',
            'message' => 'Token has expired.',
            'code'    => 5,
        ],

        'TokenInvalidException' => [
            'title'   => 'Operation Failed',
            'message' => 'Could not decode or verify token.',
            'code'    => 6,
        ],

        'PermissionException' => [
            'title'   => 'Operation Failed',
            'message' => 'User does not have the right permissions.',
            'code'    => 7,
        ],

        'RoleException' => [
            'title'   => 'Operation Failed',
            'message' => 'User does not have the right roles.',
            'code'    => 8,
        ],

        'RoleOrPermissionException' => [
            'title'   => 'Operation Failed',
            'message' => 'User does not have the right roles or permissions.',
            'code'    => 9,
        ],
    ],
];
