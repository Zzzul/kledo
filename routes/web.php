<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'docs' => url('/api/documentation'),
        'repository' => 'https://github.com/Zzzul/kledo',
    ]);
});
