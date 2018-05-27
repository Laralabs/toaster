<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Toast Display Amount
    |--------------------------------------------------------------------------
    |
    | The maximum amount of toasts that can be displayed at one time.
    |
    */
    'max_toasts' => 10,

    /*
    |--------------------------------------------------------------------------
    | Toast Stagger
    |--------------------------------------------------------------------------
    |
    | Enable/Disable stagger function, this uses the Toast Lifetime and
    | Toast Interval defined below.
    |
    */
    'toast_stagger' => true,

    /*
    |--------------------------------------------------------------------------
    | Toast Stagger All
    |--------------------------------------------------------------------------
    |
    | If true, stagger all toasts starting with the first group added.
    | If false, reset the lifetime for each group.
    |
    */
    'toast_stagger_all' => true,

    /*
    |--------------------------------------------------------------------------
    | Toast Lifetime
    |--------------------------------------------------------------------------
    |
    | When a toast is not set as important, this is the amount of time it
    | stays visible for (Milliseconds).
    |
    */
    'toast_lifetime' => 2000,

    /*
    |--------------------------------------------------------------------------
    | Toast Interval
    |--------------------------------------------------------------------------
    |
    | The amount of time between each toast closing (Milliseconds).
    |
    */
    'toast_interval' => 500,

    /*
    |--------------------------------------------------------------------------
    | Toast Position
    |--------------------------------------------------------------------------
    |
    | The position of the toast on the page, i.e. 'top left', 'top right',
    | 'bottom right' and 'bottom left'.
    |
    */
    'toast_position' => 'top right',

    /*
    |--------------------------------------------------------------------------
    | Toast Width
    |--------------------------------------------------------------------------
    |
    | Classes specified here will be added to the toast component.
    |
    */
    'toast_width' => '300px',

    /*
    |--------------------------------------------------------------------------
    | Toast Classes
    |--------------------------------------------------------------------------
    |
    | Classes specified here will be added to the toast component.
    |
    */
    'toast_classes' => [],

    /*
    |--------------------------------------------------------------------------
    | Reverse Order
    |--------------------------------------------------------------------------
    |
    | Show toasts in reverse order.
    |
    */
    'reverse_order' => false,

    /*
    |--------------------------------------------------------------------------
    | Animation Type
    |--------------------------------------------------------------------------
    |
    | The animation type used, i.e. 'css' or 'velocity'.
    |
    */
    'animation_type' => 'css',

    /*
    |--------------------------------------------------------------------------
    | Animation Speed
    |--------------------------------------------------------------------------
    |
    | The animation type used, i.e. 'css' or 'velocity'.
    |
    */
    'animation_speed' => 300,
];
