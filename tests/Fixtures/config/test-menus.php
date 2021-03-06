<?php

return [
    'test' => [
        'root_title' => 'root',
        'root_options' => [],
        'children' => [
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
    ],
    'test2' => [
        'root_title' => 'root',
        'root_options' => [],
        'children' => [
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
    ],
    'test_root' => [
        'root_title' => 'Custom Root',
        'root_options' => ['foo' => 'bar', 'roles' => ['ROLE_TEST_ROOT']],
        'item_options' => ['bar' => 'foo', 'foobar' => ['foobar']],
        'children' => [
            [
                'title' => 'menu3 #1',
                'options' => [],
                'children' => [
                    [
                        'title' => 'menu3 #1-1',
                        'options' => [],
                        'children' => [],
                    ],
                ],
            ],
        ],
    ],
];
