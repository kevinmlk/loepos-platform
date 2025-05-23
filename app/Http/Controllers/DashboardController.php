<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->take(4)->get();
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

        $dailyCounts = Document::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DAYNAME(created_at) as day, COUNT(*) as total')
            ->groupByRaw('DAYNAME(created_at)')
            ->orderByRaw("FIELD(DAYNAME(created_at), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')")
            ->get()
            ->pluck('total', 'day')
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
