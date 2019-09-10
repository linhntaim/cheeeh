<?php

return [
    'def' => [
        'abort' => [
            '403' => 'Not authorized',
            '404' => 'Not found',
        ],
    ],

    'exceptions' => [
        'app_exception' => [
            'level_failed' => 'Something went wrong with application',
            'level' => ':message',
        ],
        'database_exception' => [
            'level_failed' => 'Something went wrong with database',
            'level' => ':message',
        ],
        'exception' => [
            'level_failed' => 'Something went wrong',
            'level' => ':message',
        ],
        'unhandled_exception' => [
            'level_failed' => 'Something went wrong',
            'level' => ':message',
        ],
        'user_exception' => [
            'level_failed' => 'Something went wrong with user\'s action',
            'level' => ':message',
        ],
    ],

    'rules' => [
        'current_password' => 'The current password must be matched',
        'not_trashed' => 'The :attribute has already been trashed',
        'trashed' => 'The :attribute has not already been trashed',
    ],

    'utils' => [
        'files' => [
            'filer' => [
                'filer' => [
                    'file_not_found' => 'File is not found',
                ],
            ],
            'file_writer' => [
                'zip_archive_handler' => [
                    'cannot_opened' => 'Cannot open zip file',
                ],
                'zip_handler' => [
                    'opened' => 'Zip file was opened',
                    'not_opened' => 'Zip file was not opened',
                    'not_found' => 'File for zipping was not found',
                ],
            ],
            'file_helper' => [
                'directory_not_found' => 'Directory is not found',
                'directory_not_allowed' => 'Directory is not allowed to access',
                'directory_not_writable' => 'Directory is not writable',
            ],
        ],
    ],

    'model_repositories' => [
        'user_email_repository' => [
            'email_and_verified_code_not_matched' => 'Verified code is not matched with the email',
        ],
    ],
];
