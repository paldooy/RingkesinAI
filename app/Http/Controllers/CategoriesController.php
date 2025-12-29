<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->withCount('notes')->get();
        
        // Count favorite notes
        $favoritesCount = \App\Models\Note::where('user_id', Auth::id())
            ->where('is_favorite', true)
            ->count();

        return view('categories.index', compact('categories', 'favoritesCount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:50',
        ]);

        Category::create(array_merge($validated, ['user_id' => Auth::id()]));

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // Check if user owns this category
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:50',
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if user owns this category
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
