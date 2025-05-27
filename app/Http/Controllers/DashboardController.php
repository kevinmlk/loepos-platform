<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $documents = Document::where('organization_id', $user->organization_id)
            ->latest()
            ->take(4)
            ->get();
            
        $dailyUploadedDocuments = $this->getDailyUploadedDocuments();

        return view('dashboard.index', [
            'documents' => $documents,
            'dailyUploadedDocuments' => $dailyUploadedDocuments,
        ]);
    }

    /**
    * TODO: Get daily uploaded documents counts from monday to friday
    *
    * @return array
    */
    public function getDailyUploadedDocuments(): array
    {
        $startOfWeek = now()->startOfWeek(); // Monday
        $endOfWeek = now()->endOfWeek(); // Sunday

        $user = Auth::user();
        
        // Use a subquery to ensure compatibility with ONLY_FULL_GROUP_BY
        $dailyCounts = Document::where('organization_id', $user->organization_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, DAYNAME(created_at) as day_name, COUNT(*) as total')
            ->groupByRaw('DAYOFWEEK(created_at), DAYNAME(created_at)')
            ->orderByRaw('DAYOFWEEK(created_at)')
            ->get()
            ->pluck('total', 'day_name')
            ->toArray();

        // Ensure all days (Monday to Friday) are present in the result
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $result = [];
        foreach ($daysOfWeek as $day) {
            $result[$day] = $dailyCounts[$day] ?? 0;
        }

        return $result;
    }

}
