<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Document;
use App\Models\Upload;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'employee') {
            // Employee: only their own uploads and dossiers
            $latestUploads = Upload::where('user_id', $user->id)->get();
            $dossiers = $user->dossiers()->with(['documents' => function($query) {
                $query->latest()->take(3);
            }])->paginate(3);
        } elseif ($user->role === 'admin') {
            // Admin: uploads and dossiers for all users in their organization
            $latestUploads = Upload::whereHas('user', function ($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })->get();

            $dossiers = \App\Models\Dossier::whereHas('user', function ($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->with(['documents' => function($query) {
                $query->latest()->take(3);
            }, 'client'])
            ->paginate(3);
        } else {
            // Superadmin: all uploads and dossiers
            $latestUploads = Upload::all();
            $dossiers = \App\Models\Dossier::with(['documents' => function($query) {
                $query->latest()->take(3);
            }, 'client'])->paginate(3);
        }

        $dailyUploadedDocuments = $this->getDailyUploadedDocuments($user);

        return view('dashboard.index', [
            'dossiers' => $dossiers,
            'latestUploads' => $latestUploads,
            'dailyUploadedDocuments' => $dailyUploadedDocuments,
        ]);
    }

    /**
    * Get daily uploaded documents counts from monday to friday
    *
    * @param \App\Models\User|null $user
    * @return array
    */
    public function getDailyUploadedDocuments($user = null): array
    {
        $user = $user ?? Auth::user();

        $startOfWeek = now()->startOfWeek(); // Monday
        $endOfWeek = now()->endOfWeek(); // Sunday

        $documentQuery = Document::query();

        if ($user->role === 'employee') {
            $documentQuery->whereHas('dossier', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        } elseif ($user->role === 'admin') {
            $documentQuery->whereHas('dossier.user', function ($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            });
        }
        // else superadmin: no filter

        $dailyCounts = $documentQuery
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, DAYNAME(created_at) as day_name, COUNT(*) as total')
            ->groupByRaw('DAYOFWEEK(created_at), DAYNAME(created_at)')
            ->orderByRaw('DAYOFWEEK(created_at)')
            ->get()
            ->pluck('total', 'day_name')
            ->toArray();

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $result = [];
        foreach ($daysOfWeek as $day) {
            $result[$day] = $dailyCounts[$day] ?? 0;
        }

        return $result;
    }

}
