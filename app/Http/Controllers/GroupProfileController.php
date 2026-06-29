<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Services\MediaStorageService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GroupProfileController extends Controller
{
    protected FirebaseService $firebase;

    protected MediaStorageService $mediaStorage;

    public function __construct(FirebaseService $firebase, MediaStorageService $mediaStorage)
    {
        $this->firebase = $firebase;
        $this->mediaStorage = $mediaStorage;
    }

    /**
     * Display list of members
     */
    public function index(): View
    {
        $members = $this->firebase->getCollection('members');
        return view('admin.group.index', ['members' => $members]);
    }

    /**
     * Show create member form
     */
    public function create(): View
    {
        return view('admin.group.create');
    }

    /**
     * Store member in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:20',
            'prodi' => 'required|string',
            'position' => 'required|string',
            'email' => 'nullable|email',
            'instagram' => 'nullable|url',
            'whatsapp' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $storedUpload = null;

        try {
            if ($request->hasFile('photo')) {
                $storedUpload = $this->storePhoto($request);
            }

            $data = [
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'prodi' => $validated['prodi'],
                'position' => $validated['position'],
                'photo_url' => $storedUpload['url'] ?? '',
                'social_media' => [
                    'email' => $validated['email'] ?? '',
                    'instagram' => $validated['instagram'] ?? '',
                    'whatsapp' => $validated['whatsapp'] ?? '',
                ],
            ];

            $this->firebase->addDocument('members', $data);

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedUpload) {
                $this->mediaStorage->deleteByUrl($storedUpload['url'] ?? null);
            }
            report($e);
            return back()->with('error', 'Gagal menambahkan anggota: ' . $e->getMessage());
        }
    }

    /**
     * Show edit member form
     */
    public function edit(string $id): View
    {
        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);

        return view('admin.group.edit', ['member' => $member, 'id' => $id]);
    }

    /**
     * Update member
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prodi' => 'required|string',
            'position' => 'required|string',
            'email' => 'nullable|email',
            'instagram' => 'nullable|url',
            'whatsapp' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);
        $newPhotoUpload = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhotoUpload = $this->storePhoto($request);
            }

            $data = [
                'name' => $validated['name'],
                'prodi' => $validated['prodi'],
                'position' => $validated['position'],
                'social_media' => [
                    'email' => $validated['email'] ?? '',
                    'instagram' => $validated['instagram'] ?? '',
                    'whatsapp' => $validated['whatsapp'] ?? '',
                ],
            ];

            if ($newPhotoUpload) {
                $data['photo_url'] = $newPhotoUpload['url'];
            }

            $this->firebase->updateDocument('members', $id, $data);

            if ($newPhotoUpload) {
                $this->mediaStorage->deleteByUrl($member['photo_url'] ?? null);
            }

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newPhotoUpload) {
                $this->mediaStorage->deleteByUrl($newPhotoUpload['url'] ?? null);
            }
            report($e);
            return back()->with('error', 'Gagal memperbarui anggota: ' . $e->getMessage());
        }
    }

    /**
     * Delete member
     */
    public function destroy(string $id): RedirectResponse
    {
        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);

        try {
            $this->firebase->deleteDocument('members', $id);
            $this->mediaStorage->deleteByUrl($member['photo_url'] ?? null);

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }

    private function storePhoto(Request $request): array
    {
        $file = $request->file('photo');

        return $this->mediaStorage->uploadPublicFile($file, 'members', 'member');
    }
}
