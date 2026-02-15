<?php

return [
    'drive' => [
        'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
        'credentials_path' => env('GOOGLE_DRIVE_CREDENTIALS_PATH', 'google-drive-credentials.json'),
        // Email del usuario a impersonar con Domain-Wide Delegation (requiere Google Workspace)
        'impersonate_email' => env('GOOGLE_DRIVE_IMPERSONATE_EMAIL'),
    ],
];
