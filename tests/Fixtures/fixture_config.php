<?php

/**
 * This file is part of the Monolog Cascade package.
 *
 * (c) Raphael Antonmattei <rantonmattei@theorchard.com>
 * (c) The Orchard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'version' => 1,

    'formatters' => [
        'spaced' => [
            'format' => "%datetime% %channel%.%level_name%  %message%\n",
            'include_stacktraces' => true
        ],
        'dashed' => [
            'format' => "%datetime%-%channel%.%level_name% - %message%\n"
        ],
    ],
    'handlers' => [
        'console' => [
            'class' => \Monolog\Handler\StreamHandler::class,
            'level' => 'DEBUG',
            'formatter' => 'spaced',
            'stream' => 'php://stdout'
        ],

        'info_file_handler' => [
            'class' => \Monolog\Handler\StreamHandler::class,
            'level' => 'INFO',
            'formatter' => 'dashed',
            'stream' => './demo_info.log'
        ],

        'error_file_handler' => [
            'class' => \Monolog\Handler\StreamHandler::class,
            'level' => 'ERROR',
            'stream' => './demo_error.log',
            'formatter' => 'spaced'
        ],

        'group_handler' => [
            'class' => \Monolog\Handler\GroupHandler::class,
            'handlers' => [
                'console',
                'info_file_handler',
            ],
        ],

        'fingers_crossed_handler' => [
            'class' => \Monolog\Handler\FingersCrossedHandler::class,
            'handler' => 'group_handler',
        ],
    ],
    'processors' => [
        'tag_processor' => [
            'class' => \Monolog\Processor\TagProcessor::class
        ]
    ],
    'loggers' => [
        'my_logger' => [
            'handlers' => ['console', 'info_file_handler']
        ]
    ]
];
