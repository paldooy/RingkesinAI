<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Category;
use App\Models\Tag;
use App\Models\NoteShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    /**
     * Display a listing of notes
     */
    public function index(Request $request)
    {
        $query = Note::with(['category', 'tags'])
            ->where('user_id', Auth::id());

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Apply category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $notes = $query->latest()->paginate(12);

        $categories = Category::where('user_id', Auth::id())->get();

        return view('notes.index', compact('notes', 'categories'));
    }

    /**
     * Show the form for creating a new note (AI Summarize page)
     */
    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return view('notes.create', compact('categories'));
    }

    /**
     * Store a newly created note
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'emoji' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $note = Note::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'content' => $request->content,
            'summary' => $request->summary,
            'emoji' => $request->emoji ?? '📝',
            'color' => $request->color ?? '#3B82F6',
        ]);

        // Attach tags
        if ($request->has('tags') && is_array($request->tags)) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))]
                );
                $tagIds[] = $tag->id;
            }
            $note->tags()->sync($tagIds);
        }

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil dibuat!');
    }

    /**
     * Display the specified note
     */
    public function show($id)
    {
        $note = Note::with(['category', 'tags', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('notes.show', compact('note'));
    }

    /**
     * Show the form for editing the specified note
     */
    public function edit($id)
    {
        $note = Note::with(['category', 'tags'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        $categories = Category::where('user_id', Auth::id())->get();

        return view('notes.edit', compact('note', 'categories'));
    }

    /**
     * Update the specified note
     */
    public function update(Request $request, $id)
    {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'emoji' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $note->update($request->only(['title', 'content', 'summary', 'category_id', 'emoji', 'color']));

        // Update tags if provided
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))]
                );
                $tagIds[] = $tag->id;
            }
            $note->tags()->sync($tagIds);
        }

        return redirect()->route('notes.show', $note)->with('success', 'Catatan berhasil diperbarui!');
    }

    /**
     * Remove the specified note
     */
    public function destroy($id)
    {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil dihapus!');
    }

    /**
     * Share note with another user
     */
    public function share(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'can_edit' => 'boolean',
        ]);

        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $recipientUser = User::where('email', $request->email)->first();

        if ($recipientUser->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa membagikan catatan ke diri sendiri.');
        }

        // Create or update share record
        // Use query builder to avoid referencing a missing NoteShare model class
        \Illuminate\Support\Facades\DB::table('note_shares')->updateOrInsert(
            [
                'note_id' => $note->id,
                'shared_with_user_id' => $recipientUser->id,
            ],
            [
                'shared_by_user_id' => Auth::id(),
                'can_edit' => $request->boolean('can_edit'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Catatan berhasil dibagikan!');
    }
}
