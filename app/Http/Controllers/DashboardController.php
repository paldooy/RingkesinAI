<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;
use App\Models\Category;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics
        $totalNotes = Note::where('user_id', $user->id)->count();
        $totalCategories = Category::where('user_id', $user->id)->count();
        
        // Get recent notes
        $recentNotes = Note::where('user_id', $user->id)
            ->with(['category', 'tags'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('totalNotes', 'totalCategories', 'recentNotes'));
    }
}
