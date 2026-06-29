<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Services\MediaStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    protected FirebaseService $firebase;

    protected MediaStorageService $mediaStorage;

    public function __construct(FirebaseService $firebase, MediaStorageService $mediaStorage)
    {
        $this->firebase = $firebase;
        $this->mediaStorage = $mediaStorage;
    }

    /**
     * Display list of galleries
     */
    public function index(): View
    {
        $galleries = $this->firebase->getCollection('galleries');

        return view('admin.gallery.index', ['galleries' => $galleries]);
    }

    /**
     * Show create gallery form
     */
    public function create(): View
    {
        return view('admin.gallery.create');
    }

    /**
     * Show edit gallery form
     */
    public function edit(string $id): View
    {
        $gallery = $this->firebase->getDocument('galleries', $id);
        abort_if(! $gallery, 404);

        return view('admin.gallery.edit', ['gallery' => $gallery]);
    }

    /**
     * Store gallery in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:edukasi,sosialisasi,gotong-royong,dokumentasi,penutupan',
            'image' => 'required|image|max:5120',
        ]);

        $storedUpload = null;

        try {
            $file = $request->file('image');
            $storedUpload = $this->mediaStorage->uploadPublicFile($file, 'galleries', 'gallery');

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'category' => $validated['category'],
                'image_url' => $storedUpload['url'],
                'image_public_id' => $storedUpload['public_id'],
                'created_at' => now()->toIso8601String(),
            ];

            $this->firebase->addDocument('galleries', $data);

            return redirect()->route('gallery.index')
                ->with('success', 'Galeri berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedUpload) {
                $this->mediaStorage->deleteByUrl($storedUpload['url'] ?? null);
            }
            report($e);

            return back()->with('error', 'Gagal menambahkan galeri: '.$e->getMessage());
        }
    }

    /**
     * Update gallery metadata and optionally replace its image.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:edukasi,sosialisasi,gotong-royong,dokumentasi,penutupan',
            'image' => 'nullable|image|max:5120',
        ]);

        $newUpload = null;

        try {
            $gallery = $this->firebase->getDocument('galleries', $id);
            abort_if(! $gallery, 404);

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'category' => $validated['category'],
                'updated_at' => now()->toIso8601String(),
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $newUpload = $this->mediaStorage->uploadPublicFile($file, 'galleries', 'gallery');
                $data['image_url'] = $newUpload['url'];
                $data['image_public_id'] = $newUpload['public_id'];
            }

            $this->firebase->updateDocument('galleries', $id, $data);

            if ($newUpload !== null) {
                try {
                    $this->mediaStorage->deleteUploadedAsset(
                        $gallery['image_public_id'] ?? null,
                        $gallery['image_url'] ?? null
                    );
                } catch (\Throwable $cleanupError) {
                    // The Firestore update and new image are already valid.
                    // A failed cleanup must not roll them back.
                    report($cleanupError);
                }
            }

            return redirect()->route('gallery.index')
                ->with('success', 'Galeri berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newUpload) {
                $this->mediaStorage->deleteByUrl($newUpload['url'] ?? null);
            }

            report($e);

            return back()->withInput()
                ->with('error', 'Gagal memperbarui galeri: '.$e->getMessage());
        }
    }

    /**
     * Delete gallery
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $gallery = $this->firebase->getDocument('galleries', $id);
            $this->firebase->deleteDocument('galleries', $id);
            $this->mediaStorage->deleteUploadedAsset(
                $gallery['image_public_id'] ?? null,
                $gallery['image_url'] ?? null
            );

            return redirect()->route('gallery.index')
                ->with('success', 'Galeri berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus galeri: '.$e->getMessage());
        }
    }
}
