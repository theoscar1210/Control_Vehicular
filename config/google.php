<?php

return [
    'drive' => [
        'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
        'credentials_path' => env('GOOGLE_DRIVE_CREDENTIALS_PATH', 'google-drive-credentials.json'),
    ],
];
