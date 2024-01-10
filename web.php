<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::match(['get', 'post'], 'orm', function (Request $request) {

    $correctPasscode = 'admin';
    $data = [];
    if ($request->isMethod('post')) {
        if ($request->mode == 'logout') {
            session(['passcode_verified' => false]);
        } elseif ($request->mode == 'passcode' && !session('passcode_verified') && $request->passcode === $correctPasscode) {
            session(['passcode_verified' => true]);
        } elseif ($request->mode == 'editor' && !empty($request->orm_query) && session('passcode_verified')) {
            try {
                $request->orm_query = Str::replace(';', '', $request->orm_query);
                session(['query' => $request->orm_query]);
                if (Str::startsWith($request->orm_query, "DB::")) {
                    $code = "return $request->orm_query;";
                    $data = eval($code);
                } elseif (Str::contains($request->orm_query, "toSql")) {
                    $query = Str::replace('toSql', 'get', $request->orm_query);
                    $code = "return App\Models\\$query;";
                    DB::connection()->enableQueryLog();
                    eval($code);
                    $data = DB::getQueryLog();
                    DB::disableQueryLog();
                } else {
                    $code = 'return App\Models\\' . $request->orm_query . ';';
                    $data = eval($code);
                }
            } catch (\Exception $e) {
                $data = ['message' => nl2br(addslashes(htmlspecialchars($e->getMessage())))];
            }
        }
    }
    return view('orm', [
        'data' => $data,
        'orm_query' => $request->orm_query ?: ''
    ]);
})->name('orm');
