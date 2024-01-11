<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::match(['get', 'post'], 'orm', function (Request $request) {
    function getModels($path)
    {
        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $path . '/' . $result;
            if (is_dir($filename)) {
                $out = array_merge($out, getModels($filename));
            } else {
                if (pathinfo($result, PATHINFO_EXTENSION) != 'php') continue;
                $out[] = substr($result, 0, -4);
            }
        }
        return $out;
    }

    $models = getModels(app_path() . "/Models");
    sort($models);

    $correctPasscode = 'admin';
    $data = [];
    if ($request->isMethod('post')) {
        //I have ignored the DML operation here
        if (Str::contains($request->orm_query, ['create', 'delete', 'update', 'insert'])) {
            return view('orm', ['data' => [], 'models' => $models]);
        }
        if ($request->mode == 'logout') {
            session(['passcode_verified' => false]);
        } elseif ($request->mode == 'passcode' && !session('passcode_verified') && $request->passcode === $correctPasscode) {
            session(['passcode_verified' => true]);
        } elseif ($request->mode == 'editor' && !empty($request->orm_query) && session('passcode_verified')) {
            try {
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

    if ($request->isMethod('get')) {
        if (!empty($request->model) && session('passcode_verified')) {
            //I have ignored the DML operation here
            if (Str::contains($request->model, ['create', 'delete', 'update', 'insert'])) {
                return view('orm', ['data' => [], 'models' => $models]);
            }
            try {
                    if(!empty($request->model)){
                        $request->model = $request->model . '::query()
->take(25)
->get();';

                        session(['query' => $request->model]);
                        $code = 'return App\Models\\' . $request->model . ';';
                        $data = eval($code);
                    }
            } catch (\Exception $e) {
                $data = ['message' => nl2br(addslashes(htmlspecialchars($e->getMessage())))];
            }
        }
    }

    return view('orm', [
        'data' => $data,
        'models' => $models,
    ]);
})->name('orm');
