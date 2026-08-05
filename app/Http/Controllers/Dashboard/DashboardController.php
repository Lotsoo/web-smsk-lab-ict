<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\SuratRevisi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        if (!Auth::check()) {
            return redirect('/login')->with('loginError', 'Login Required!');
        }
        $totalSuratMasuk   = SuratMasuk::count();
        $totalSuratKeluar  = SuratKeluar::count();
        $revisiPending     = SuratRevisi::where('status', 'menunggu')->count();
        $berhasilDisetujui = SuratKeluar::where('status', 'disetujui')->count();

        // Get recent activities from activity_logs
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard.dashboard',compact(
            'totalSuratMasuk',
            'totalSuratKeluar',
            'revisiPending',
            'berhasilDisetujui',
            'recentActivities'
        ));
    }


}
