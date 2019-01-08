<?php

namespace App\Http\Controllers;

use App\Helpers\IndexHelper;

class HomeController extends Controller
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
    public function index()
    {
        $checks = app('elasticsearch')->search([
            'index' => IndexHelper::generateMonitoringIndex(),
            'type' => 'doc',
            'size' => 1000,
            'body' => [
            ],
        ])['hits']['hits'];

        return view('home', ['checks' => $checks]);
    }
}
