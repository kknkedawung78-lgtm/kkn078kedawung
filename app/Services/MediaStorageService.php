<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorageService
{
    public function __construct(
        private readonly FirebaseService $firebase
    ) {
    }

    public function uploadPublicFile(UploadedFile $file, string $directory, string $prefix): array
    {
        $extension = $file->extension() ?: $file->guessExtension() ?: 'bin';
        $filename = uniqid($prefix.'_', true).'.'.$extension;
        $objectPath = trim($directory, '/').'/'.$filename;
        $downloadToken = (string) Str::uuid();

        $bucket = $this->firebase->storage()->getBucket();
        $bucket->upload(
            fopen($file->getRealPath(), 'r'),
            [
                'name' => $objectPath,
                'metadata' => [
                    'contentType' => $file->getMimeType() ?: 'application/octet-stream',
                    'metadata' => [
                        'firebaseStorageDownloadTokens' => $downloadToken,
                    ],
                ],
            ]
        );

        return [
            'path' => $objectPath,
            'url' => sprintf(
                'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=%s',
                config('firebase.storage.bucket'),
                rawurlencode($objectPath),
                $downloadToken
            ),
        ];
    }

    public function deleteByUrl(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = rawurldecode((string) parse_url($url, PHP_URL_PATH));
        $storagePosition = strpos($path, '/storage/');
        if ($storagePosition !== false) {
            Storage::disk('public')->delete(substr($path, $storagePosition + strlen('/storage/')));

            return;
        }

        $firebaseMarker = '/o/';
        $firebasePosition = strpos($path, $firebaseMarker);
        if ($firebasePosition !== false) {
            $objectPath = substr($path, $firebasePosition + strlen($firebaseMarker));
            if ($objectPath !== '') {
                $this->firebase->storage()->getBucket()->object($objectPath)->delete();
            }

            return;
        }

        $objectPath = ltrim($path, '/');
        if ($objectPath !== '') {
            $this->firebase->storage()->getBucket()->object($objectPath)->delete();
        }
    }
}
