<?php

namespace App\Http\Controllers\Api;

use App\Helpers\IndexHelper;
use App\Http\Controllers\Controller;

class ChecksController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $checks = app('elasticsearch')->search([
            'index' => IndexHelper::generateMonitoringIndex(),
            'type' => 'doc',
            'size' => 1000,
            'body' => [
            ],
        ])['hits']['hits'];

        return response()->json(['success' => (count($checks)) ? true : false, 'data' => $checks]);
    }
}
