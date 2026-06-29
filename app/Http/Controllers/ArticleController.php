<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Services\MediaStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    protected FirebaseService $firebase;

    protected MediaStorageService $mediaStorage;

    public function __construct(FirebaseService $firebase, MediaStorageService $mediaStorage)
    {
        $this->firebase = $firebase;
        $this->mediaStorage = $mediaStorage;
    }

    /**
     * Display list of articles
     */
    public function index(): View
    {
        $articles = $this->firebase->getCollection('articles');

        return view('admin.artikel.index', ['articles' => $articles]);
    }

    /**
     * Show create article form
     */
    public function create(): View
    {
        return view('admin.artikel.create');
    }

    /**
     * Store article in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'author' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $storedUpload = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $storedUpload = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'slug' => str()->slug($validated['title']),
                'content' => $validated['content'],
                'category' => $validated['category'],
                'author' => $validated['author'],
                'published_at' => now()->toIso8601String(),
                'thumbnail_url' => $storedUpload['url'] ?? '',
                'thumbnail_public_id' => $storedUpload['public_id'] ?? '',
                'gallery' => [],
            ];

            $this->firebase->addDocument('articles', $data);

            return redirect()->route('artikel.index')
                ->with('success', 'Artikel berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedUpload) {
                $this->mediaStorage->deleteByUrl($storedUpload['url'] ?? null);
            }
            report($e);

            return back()->withInput()->with('error', 'Gagal menambahkan artikel: '.$e->getMessage());
        }
    }

    /**
     * Show edit article form
     */
    public function edit(string $id): View
    {
        $article = $this->firebase->getDocument('articles', $id);
        abort_if(! $article, 404);

        return view('admin.artikel.edit', ['article' => $article, 'id' => $id]);
    }

    /**
     * Update article
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'author' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $article = $this->firebase->getDocument('articles', $id);
        abort_if(! $article, 404);
        $newThumbnailUpload = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $newThumbnailUpload = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'slug' => str()->slug($validated['title']),
                'content' => $validated['content'],
                'category' => $validated['category'],
                'author' => $validated['author'],
            ];

            if ($newThumbnailUpload) {
                $data['thumbnail_url'] = $newThumbnailUpload['url'];
                $data['thumbnail_public_id'] = $newThumbnailUpload['public_id'];
            }

            $this->firebase->updateDocument('articles', $id, $data);

            if ($newThumbnailUpload) {
                $this->mediaStorage->deleteUploadedAsset(
                    $article['thumbnail_public_id'] ?? null,
                    $article['thumbnail_url'] ?? null
                );
            }

            return redirect()->route('artikel.index')
                ->with('success', 'Artikel berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newThumbnailUpload) {
                $this->mediaStorage->deleteByUrl($newThumbnailUpload['url'] ?? null);
            }
            report($e);

            return back()->withInput()->with('error', 'Gagal memperbarui artikel: '.$e->getMessage());
        }
    }

    /**
     * Delete article
     */
    public function destroy(string $id): RedirectResponse
    {
        $article = $this->firebase->getDocument('articles', $id);
        abort_if(! $article, 404);

        try {
            $this->firebase->deleteDocument('articles', $id);
            $this->mediaStorage->deleteUploadedAsset(
                $article['thumbnail_public_id'] ?? null,
                $article['thumbnail_url'] ?? null
            );

            return redirect()->route('artikel.index')
                ->with('success', 'Artikel berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus artikel: '.$e->getMessage());
        }
    }

    private function storeThumbnail(Request $request): array
    {
        $file = $request->file('thumbnail');

        return $this->mediaStorage->uploadPublicFile($file, 'articles', 'article');
    }
}
