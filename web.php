<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::match(['get', 'post'], 'orm', function (Request $request) {
    $data = [];

    if ($request->method() == 'POST') {
        try {
            $code = "return App\Models\\$request->orm_query->toArray();";
            $data = eval($code);
        } catch (\Exception $e) {
            $data = ['message' => nl2br(addslashes(htmlspecialchars($e->getMessage())))];
        }
    }

    return view('orm', [
        'data' => $data,
        'orm_query' => $request->orm_query ?: ''
    ]);
})->name('orm');
