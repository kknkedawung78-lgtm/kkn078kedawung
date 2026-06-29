<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Services\MediaStorageService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WorkProgramController extends Controller
{
    protected FirebaseService $firebase;

    protected MediaStorageService $mediaStorage;

    public function __construct(FirebaseService $firebase, MediaStorageService $mediaStorage)
    {
        $this->firebase = $firebase;
        $this->mediaStorage = $mediaStorage;
    }

    /**
     * Display list of work programs
     */
    public function index(): View
    {
        $programs = $this->firebase->getCollection('work_programs');
        return view('admin.program.index', ['programs' => $programs]);
    }

    /**
     * Show create program form
     */
    public function create(): View
    {
        return view('admin.program.create');
    }

    /**
     * Store program in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objective' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $storedUpload = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $storedUpload = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'objective' => $validated['objective'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'thumbnail_url' => $storedUpload['url'] ?? '',
                'gallery' => [],
            ];

            $this->firebase->addDocument('work_programs', $data);

            return redirect()->route('program.index')
                ->with('success', 'Program kerja berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedUpload) {
                $this->mediaStorage->deleteByUrl($storedUpload['url'] ?? null);
            }
            report($e);
            return back()->with('error', 'Gagal menambahkan program: ' . $e->getMessage());
        }
    }

    /**
     * Show edit program form
     */
    public function edit(string $id): View
    {
        $program = $this->firebase->getDocument('work_programs', $id);
        abort_if(!$program, 404);

        return view('admin.program.edit', ['program' => $program, 'id' => $id]);
    }

    /**
     * Update program
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objective' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $program = $this->firebase->getDocument('work_programs', $id);
        abort_if(!$program, 404);
        $newThumbnailUpload = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $newThumbnailUpload = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'objective' => $validated['objective'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
            ];

            if ($newThumbnailUpload) {
                $data['thumbnail_url'] = $newThumbnailUpload['url'];
            }

            $this->firebase->updateDocument('work_programs', $id, $data);

            if ($newThumbnailUpload) {
                $this->mediaStorage->deleteByUrl($program['thumbnail_url'] ?? null);
            }

            return redirect()->route('program.index')
                ->with('success', 'Program berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newThumbnailUpload) {
                $this->mediaStorage->deleteByUrl($newThumbnailUpload['url'] ?? null);
            }
            report($e);
            return back()->with('error', 'Gagal memperbarui program: ' . $e->getMessage());
        }
    }

    /**
     * Delete program
     */
    public function destroy(string $id): RedirectResponse
    {
        $program = $this->firebase->getDocument('work_programs', $id);
        abort_if(!$program, 404);

        try {
            $this->firebase->deleteDocument('work_programs', $id);
            $this->mediaStorage->deleteByUrl($program['thumbnail_url'] ?? null);

            return redirect()->route('program.index')
                ->with('success', 'Program berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus program: ' . $e->getMessage());
        }
    }

    private function storeThumbnail(Request $request): array
    {
        $file = $request->file('thumbnail');

        return $this->mediaStorage->uploadPublicFile($file, 'programs', 'program');
    }
}
