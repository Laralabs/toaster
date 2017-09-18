<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JavaScript Namespace
    |--------------------------------------------------------------------------
    |
    | By default, we'll add variables to the toaster namespace. However, this
    | can be changed to something of your preference. Avoid using 'window'.
    |
    */
    'js_namespace' => 'toaster',

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
];
