<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource with optional country filter
     */
    public function index(Request $request)
    {
        $query = Content::with(['user.details'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');

        // Advanced DB query: Filter by author's country
        if ($request->has('country')) {
            $query->whereHas('user.details', function($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        // Another advanced query: Get only featured content
        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        return response()->json($query->get());
    }

    /**
     * Get featured content with author details (for carousel)
     */
    public function featured()
    {
        $contents = Content::with(['user.details'])
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($contents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'sometimes|boolean'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('content_images', 'public');
        }

        $content = Content::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
            'image_path' => $imagePath,
            'is_featured' => $validated['is_featured'] ?? false
        ]);

        return response()->json($content, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $content = Content::with(['user.details'])->findOrFail($id);
        return response()->json($content);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $content = Content::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'is_featured' => 'sometimes|boolean',
        ]);

        $content->update($validated);

        return response()->json($content);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Content::destroy($id);
        return response()->json(null, 204);
    }
}
