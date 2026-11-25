<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class GitHubImageService
{
    private function configured(): void
    {
        foreach (['token', 'owner', 'repo'] as $key) {
            if (! config("github_media.$key")) {
                throw new RuntimeException("GitHub media configuration is missing: $key");
            }
        }
    }

    public function upload(UploadedFile $file, string $subfolder): string
    {
        $this->configured();
        $name = Str::uuid().'.'.strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $path = trim(config('github_media.folder').'/'.$subfolder.'/'.$name, '/');
        $url = $this->api($path);
        $response = Http::withToken(config('github_media.token'))->acceptJson()->put($url, [
            'message' => 'Upload website image '.$path,
            'branch' => config('github_media.branch', 'main'),
            'content' => base64_encode(file_get_contents($file->getRealPath())),
        ]);
        if (! $response->successful() || ! $response->json('content.download_url')) {
            Log::error('GitHub image upload failed', ['path' => $path, 'status' => $response->status()]);
            throw new RuntimeException('Image upload to GitHub failed. Check repository access and try again.');
        }
        return $response->json('content.download_url');
    }

    public function delete(?string $url): void
    {
        $path = $this->pathFromUrl($url);
        if (! $path) return;
        $this->configured();
        $meta = Http::withToken(config('github_media.token'))->acceptJson()->get($this->api($path), ['ref' => config('github_media.branch', 'main')]);
        if ($meta->status() === 404) return;
        if (! $meta->successful() || ! $meta->json('sha')) {
            Log::warning('GitHub image lookup before deletion failed', ['path' => $path, 'status' => $meta->status()]);
            throw new RuntimeException('Could not locate the previous GitHub image for deletion.');
        }
        $deleted = Http::withToken(config('github_media.token'))->acceptJson()->delete($this->api($path), [
            'message' => 'Delete replaced website image '.$path,
            'sha' => $meta->json('sha'),
            'branch' => config('github_media.branch', 'main'),
        ]);
        if (! $deleted->successful()) {
            Log::warning('GitHub image deletion failed', ['path' => $path, 'status' => $deleted->status()]);
            throw new RuntimeException('Could not delete the previous GitHub image.');
        }
    }

    public function diagnostic(): array
    {
        $this->configured();
        $response = Http::withToken(config('github_media.token'))->acceptJson()->get('https://api.github.com/repos/'.rawurlencode(config('github_media.owner')).'/'.rawurlencode(config('github_media.repo')));
        if (! $response->successful()) {
            return ['repository_accessible' => false, 'write_access' => false, 'status' => $response->status(), 'branch' => config('github_media.branch', 'main')];
        }
        $path = trim(config('github_media.folder').'/diagnostics/'.Str::uuid().'.txt', '/');
        $probe = Http::withToken(config('github_media.token'))->acceptJson()->put($this->api($path), [
            'message' => 'Verify website media storage access',
            'branch' => config('github_media.branch', 'main'),
            'content' => base64_encode('Temporary GitHub media permission check.'),
        ]);
        if (! $probe->successful() || ! $probe->json('content.sha')) {
            Log::warning('GitHub media write diagnostic failed', ['status' => $probe->status()]);
            return ['repository_accessible' => true, 'write_access' => false, 'status' => $probe->status(), 'branch' => config('github_media.branch', 'main')];
        }
        $sha = $probe->json('content.sha');
        $cleanup = Http::withToken(config('github_media.token'))->acceptJson()->delete($this->api($path), [
            'message' => 'Remove temporary media permission check', 'sha' => $sha, 'branch' => config('github_media.branch', 'main'),
        ]);
        return ['repository_accessible' => true, 'write_access' => true, 'temporary_file_removed' => $cleanup->successful(), 'status' => $response->status(), 'branch' => config('github_media.branch', 'main')];
    }

    private function api(string $path): string
    {
        return 'https://api.github.com/repos/'.rawurlencode(config('github_media.owner')).'/'.rawurlencode(config('github_media.repo')).'/contents/'.implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    private function pathFromUrl(?string $url): ?string
    {
        if (! $url || ! str_contains($url, 'raw.githubusercontent.com/')) return null;
        $parts = explode('/', parse_url($url, PHP_URL_PATH) ?? '');
        if (count($parts) < 5 || $parts[1] !== config('github_media.owner') || strtolower($parts[2]) !== strtolower(config('github_media.repo'))) return null;
        $path = implode('/', array_slice($parts, 4));
        return str_starts_with($path, trim(config('github_media.folder').'/','/')) ? $path : null;
    }
}
