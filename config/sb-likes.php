<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Reactions
    |--------------------------------------------------------------------------
    |
    | Define the available reaction types. Each reaction has a name, emoji,
    | Lottie animation file (optional), and display color.
    |
    */
    'reactions' => [
        'like' => [
            'name' => 'Like',
            'emoji' => '👍',
            'lottie' => 'like.json',
            'color' => '#2078f4',
        ],
        'love' => [
            'name' => 'Love',
            'emoji' => '❤️',
            'lottie' => 'love.json',
            'color' => '#f33e58',
        ],
        'haha' => [
            'name' => 'Haha',
            'emoji' => '😂',
            'lottie' => 'haha.json',
            'color' => '#f7b125',
        ],
        'wow' => [
            'name' => 'Wow',
            'emoji' => '😮',
            'lottie' => 'wow.json',
            'color' => '#f7b125',
        ],
        'sad' => [
            'name' => 'Sad',
            'emoji' => '😢',
            'lottie' => 'sad.json',
            'color' => '#f7b125',
        ],
        'angry' => [
            'name' => 'Angry',
            'emoji' => '😡',
            'lottie' => 'angry.json',
            'color' => '#e9710f',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Reaction
    |--------------------------------------------------------------------------
    |
    | The reaction used when user clicks the button without selecting from
    | the reaction picker (simple like).
    |
    */
    'default_reaction' => 'like',

    /*
    |--------------------------------------------------------------------------
    | Show Counts
    |--------------------------------------------------------------------------
    |
    | Whether to show the total reaction count by default.
    |
    */
    'show_counts' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Reaction Summary
    |--------------------------------------------------------------------------
    |
    | Whether to show the breakdown of reactions (top 3 reaction types).
    |
    */
    'show_summary' => true,

    /*
    |--------------------------------------------------------------------------
    | Animation Duration
    |--------------------------------------------------------------------------
    |
    | Duration of the Lottie animation in milliseconds.
    |
    */
    'animation_duration' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Picker Position
    |--------------------------------------------------------------------------
    |
    | Default position of the reaction picker: 'top', 'bottom', 'left', 'right'
    |
    */
    'picker_position' => 'top',

    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    |
    | The table name used to store reactions.
    |
    */
    'table' => 'ld_reactions',

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model class for relationships.
    |
    */
    'user_model' => \App\Models\User::class,
];
