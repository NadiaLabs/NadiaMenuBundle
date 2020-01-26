<?php

return [
    'test' => [
        [
            'title' => 'menu #1',
            'options' => [
                'roles' => ['ROLE_PAGE1'],
            ],
            'children' => [
                [
                    'title' => 'menu #1-1',
                    'options' => [
                        'roles' => ['ROLE_PAGE1'],
                    ],
                    'children' => [],
                ],
                [
                    'title' => 'menu #1-2',
                    'options' => [
                        'roles' => ['ROLE_PAGE1'],
                    ],
                    'children' => [],
                ],
            ],
        ],
        [
            'title' => 'menu #2',
            'options' => [
                'roles' => ['ROLE_PAGE2'],
            ],
            'children' => [
                [
                    'title' => 'menu #2-1',
                    'options' => [
                        'roles' => ['ROLE_PAGE2'],
                    ],
                    'children' => [
                        [
                            'title' => 'menu #2-1-1',
                            'options' => [],
                            'children' => [],
                        ],
                    ],
                ]
            ],
        ],
        [
            'title' => 'menu #3',
            'options' => [
                'roles' => ['ROLE_PAGE3'],
            ],
            'children' => [
                [
                    'title' => 'menu #3-1',
                    'options' => [
                        'roles' => ['ROLE_PAGE3'],
                    ],
                    'children' => [],
                ],
            ],
        ],
    ],
    'test2' => [
        [
            'title' => 'menu2 #1',
            'options' => [],
            'children' => [
                [
                    'title' => 'menu2 #1-1',
                    'options' => [],
                    'children' => [],
                ],
            ],
        ],
        [
            'title' => 'menu2 #2',
            'options' => [],
            'children' => [
                [
                    'title' => 'menu2 #2-1',
                    'options' => [],
                    'children' => [
                        [
                            'title' => 'menu2 #2-1-1',
                            'options' => [],
                            'children' => [],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
