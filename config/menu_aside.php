<?php
// Aside menu
return [

    'items' => [
        // Dashboard
        [
            'title' => 'Dashboard',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => '/',
            'new-tab' => false,
        ],

        // Custom
        [
            'section' => 'Administrator',
        ],
        [
            'title' => 'Applications',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Users',
                    'bullet' => 'dot',
                    'submenu' => [
                        
                        [
                            'title' => 'List User',
                            'page' => 'custom/apps/user/list-columns-2'
                        ],
                        [
                            'title' => 'Add User',
                            'page' => 'custom/apps/user/add-user'
                        ],
                        [
                            'title' => 'Edit User',
                            'page' => 'custom/apps/user/edit-user'
                        ],
                    ]
                ],
                [
                    'title' => 'Profile',
                    'bullet' => 'dot',
                    'submenu' => [
                        [
                            'title' => 'My Profile',
                            'bullet' => 'line',
                            'submenu' => [
                                [
                                    'title' => 'Overview',
                                    'page' => 'custom/apps/profile/profile-1/overview'
                                ],
                                [
                                    'title' => 'Personal Information',
                                    'page' => 'custom/apps/profile/profile-1/personal-information'
                                ],
                                [
                                    'title' => 'Account Information',
                                    'page' => 'custom/apps/profile/profile-1/account-information'
                                ],
                                [
                                    'title' => 'Change Password',
                                    'page' => 'custom/apps/profile/profile-1/change-password'
                                ],
                                [
                                    'title' => 'Email Settings',
                                    'page' => 'custom/apps/profile/profile-1/email-settings'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'Inbox',
                    'bullet' => 'dot',
                    'page' => 'custom/apps/inbox',
                    'label' => [
                        'type' => 'label-danger label-inline',
                        'value' => 'new'
                    ]
                ]
            ]
        ],
        // Custom
        [
            'section' => 'HR',
        ],

        [
            'title' => 'HR',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    [
                        'title' => 'Data KTP',
                        'page' => 'identity/list'
                    ],
                    [
                        'title' => 'Data Karyawan',
                        'page' => 'employee/list'
                    ],
                ],
            ],
        ],
        // Custom
        [
            'section' => 'Transaksi',
        ],
        [
            'title' => 'Purchasing',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    [
                        'title' => 'Permintaan Pembelian',
                        'page' => 'spp'
                    ],
                
                    
                    [
                        'title' => 'Order Pembelian',
                        'page' => 'custom/pages/wizard/wizard-2'
                    ],
                    [
                        'title' => 'Uraian Pembelian',
                        'page' => 'custom/pages/wizard/wizard-3'
                    ],
                    [
                        'title' => 'Approval Pembelian',
                        'page' => 'custom/pages/wizard/wizard-4'
                    ],
                    [
                        'title' => 'Release Order Pembelian',
                        'page' => 'custom/pages/wizard/wizard-4'
                    ],
                    [
                        'title' => 'Penutupan Order Pembelian',
                        'page' => 'custom/pages/wizard/wizard-4'
                    ],
                    [
                        'title' => 'Penutupan Permintaan Pembelian',
                        'page' => 'custom/pages/wizard/wizard-4'
                    ]
                    
                ],
                
            ],
        ]
    ]

];
