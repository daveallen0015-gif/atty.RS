<?php

return [
    'token' => env('GITHUB_MEDIA_TOKEN', env('GITHUB_TOKEN')),
    'owner' => env('GITHUB_MEDIA_OWNER', env('GITHUB_OWNER')),
    'repo' => env('GITHUB_MEDIA_REPO', env('GITHUB_REPO')),
    'branch' => env('GITHUB_MEDIA_BRANCH', env('GITHUB_BRANCH', 'main')),
    'folder' => trim(env('GITHUB_MEDIA_FOLDER', env('GITHUB_IMAGE_FOLDER', 'files')), '/'),
];
