<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::match(['get', 'post'], 'orm', function (Request $request) {

    $correctPasscode = 'aatif';
    $data = [];

    if ($request->method() == 'POST' && $request->mode == 'logout') {
        session(['passcode_verified' => false]);
    }

    if ($request->method() == 'POST' && $request->mode == 'passcode') {
        if (!session('passcode_verified') && $request->passcode === $correctPasscode)
            session(['passcode_verified' => true]);
    }

    if ($request->method() == 'POST' && $request->mode == 'editor') {
        try {

            $request->orm_query = Str::replace(';', '', $request->orm_query);
            if (Str::startsWith($request->orm_query, "DB::")) {
                $code = "return $request->orm_query;";
                $data = eval($code);
            } elseif (Str::contains($request->orm_query, "toSql")) {
                $query = Str::replace('toSql', 'get', $request->orm_query);
                $code = "return App\Models\\$query;";
                Illuminate\Support\Facades\DB::connection()->enableQueryLog();
                eval($code);
//                $data = collect(DB::getQueryLog())->pluck('query')->toArray();
//                $bindings = collect(DB::getQueryLog())->pluck('bindings')->toArray();
                $data = Illuminate\Support\Facades\DB::getQueryLog();
                Illuminate\Support\Facades\DB::disableQueryLog();
            } else {
                $code = "return App\Models\\$request->orm_query->toArray();";
                $data = eval($code);
            }

        } catch (\Exception $e) {
            $data = ['message' => nl2br(addslashes(htmlspecialchars($e->getMessage())))];
        }
    }

    return view('orm', [
        'data' => $data,
        'orm_query' => $request->orm_query ?: ''
    ]);
})->name('orm');
