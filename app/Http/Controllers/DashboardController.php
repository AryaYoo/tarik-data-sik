<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExtractionLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonthCount = ExtractionLog::whereMonth('created_at', Carbon::now()->month)->count();
        $totalCount = ExtractionLog::count();

        return view('dashboard', compact('currentMonthCount', 'totalCount'));
    }
}
