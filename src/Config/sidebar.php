<?php

return [
    [
        'title'      => 'Dashboard',
        'icon'       => 'fas fa-tachometer-alt',
        'route'      => 'admin.dashboard',
        'icon_color' => 'text-encodex-secondary',
        'for_active' => 'admin.dashboard',
        'sl'         => 1,
    ],
    [
        'title'      => 'Loans',
        'icon'       => 'fas fa-money-check-alt',
        'icon_color' => 'icc-1',
        'sl'         => 2,
        'children'   => [
            [
                'icon'       => 'fas fa-list',
                'title'      => 'All Loans',
                'route'      => 'admin.loans.index',
                'permit'     => 'loan.show',
                'icon_color' => 'icc-2',
                'for_active' => 'admin.loans',
            ],
            [
                'icon'       => 'fas fa-user-lock',
                'title'      => 'Loan Users',
                'route'      => 'admin.loan-users.index',
                'permit'     => 'loan_user.show',
                'icon_color' => 'icc-3',
                'for_active' => 'admin.loan-users',
            ],
            [
                'icon'       => 'fas fa-calendar-check',
                'title'      => 'Payment Planner',
                'route'      => 'admin.loans.payment-planner',
                'permit'     => 'loan.show',
                'icon_color' => 'icc-3',
                'for_active' => 'admin.loans.payment-planner',
            ],
        ]
    ],

    [
        'title'      => 'Daily Expense',
        'icon'       => 'fas fa-receipt',
        'icon_color' => 'icc-11',
        'route'      => 'admin.daily-expenses.index',
        'permit'     => 'daily-expense.show',
        'for_active' => 'admin.daily-expenses',
        'sl'         => 3,
    ],


    [
        'title'      => 'Drive',
        'icon'       => 'fas fa-hdd',
        'icon_color' => 'icc-21',
        'route'      => 'admin.drive',
        'permit'     => 'drive.view',
        'for_active' => 'admin.drive',
        'sl'         => 4,
    ],
    [
        'title'      => 'Disk',
        'icon'       => 'fas fa-compact-disc',
        'icon_color' => 'icc-33',
        'route'      => 'admin.disks.index',
        'permit'     => 'disk.show',
        'for_active' => 'admin.disks',
        'sl'         => 5,
    ],
    [
        'title'      => 'Gallery',
        'icon'       => 'fas fa-images',
        'icon_color' => 'icc-44',
        'route'      => 'admin.gallery.index',
        'permit'     => 'gallery.action',
        'for_active' => 'admin.gallery',
        'sl'         => 6,
    ],
    [
        'title'      => 'Messages',
        'icon'       => 'fas fa-envelope',
        'icon_color' => 'icc-55',
        'route'      => 'admin.messages.index',
        'permit'     => 'message.show',
        'for_active' => 'admin.messages',
        'sl'         => 7,
    ],
    [
        'title'      => 'Events',
        'icon'       => 'fas fa-calendar-alt',
        'icon_color' => 'icc-66',
        'route'      => 'admin.events.index',
        'permit'     => 'event.show',
        'for_active' => 'admin.events',
        'sl'         => 8,
    ],
    [
        'title'      => 'HerCycle',
        'icon'       => 'fas fa-heart',
        'icon_color' => 'icc-77',
        'route'      => 'admin.hercycle.index',
        'permit'     => 'hercycle.show',
        'for_active' => 'admin.hercycle',
        'sl'         => 9,
    ],
    [
        'permit'     => 'setting.edit',
        'title'      => 'Settings',
        'icon'       => 'fas fa-cog',
        'route'      => 'admin.settings.edit',
        'icon_color' => 'icc-100',
        'for_active' => 'admin.settings',
        'sl'         => 10,
    ],
    // [
    //     'title'      => 'User Management',
    //     'icon'       => 'fas fa-users-cog',
    //     'icon_color' => 'icc-88',
    //     'sl'         => 11,
    //     'children'   => [
    //         [
    //             'icon'       => 'fas fa-users',
    //             'title'      => 'Users',
    //             'route'      => 'admin.users.index',
    //             'permit'     => 'user.view',
    //             'icon_color' => 'icc-87',
    //             'for_active' => 'admin.users',
    //         ],
    //         [
    //             'icon'       => 'fas fa-user-shield',
    //             'title'      => 'Roles',
    //             'route'      => 'admin.roles.index',
    //             'permit'     => 'role.view',
    //             'icon_color' => 'icc-86',
    //             'for_active' => 'admin.roles',
    //         ],
    //     ]
    // ],




];



