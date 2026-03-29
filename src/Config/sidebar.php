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
                'icon_color' => 'icc-1',
                'for_active' => 'admin.loans',
            ],
            [
                'icon'       => 'fas fa-user-lock',
                'title'      => 'Loan Users',
                'route'      => 'admin.loan-users.index',
                'permit'     => 'loan_user.show',
                'icon_color' => 'icc-2',
                'for_active' => 'admin.loan-users',
            ],
        ]
    ],

    [
        'title'      => 'Daily Expense',
        'icon'       => 'fas fa-receipt',
        'icon_color' => 'icc-3',
        'route'      => 'admin.daily-expenses.index',
        'permit'     => 'daily-expense.show',
        'for_active' => 'admin.daily-expenses',
        'sl'         => 3,
    ],


    [
        'title'      => 'Drive',
        'icon'       => 'fas fa-hdd',
        'icon_color' => 'icc-4',
        'route'      => 'admin.drive',
        'permit'     => 'drive.view',
        'for_active' => 'admin.drive',
        'sl'         => 4,
    ],
    [
        'title'      => 'Disk',
        'icon'       => 'fas fa-compact-disc',
        'icon_color' => 'icc-5',
        'route'      => 'admin.disks.index',
        'permit'     => 'disk.show',
        'for_active' => 'admin.disks',
        'sl'         => 5,
    ],
    [
        'title'      => 'Gallery',
        'icon'       => 'fas fa-images',
        'icon_color' => 'icc-6',
        'route'      => 'admin.gallery.index',
        'permit'     => 'gallery.action',
        'for_active' => 'admin.gallery',
        'sl'         => 6,
    ],
    [
        'title'      => 'Messages',
        'icon'       => 'fas fa-envelope',
        'icon_color' => 'icc-7',
        'route'      => 'admin.messages.index',
        'permit'     => 'message.show',
        'for_active' => 'admin.messages',
        'sl'         => 7,
    ],
    [
        'title'      => 'Events',
        'icon'       => 'fas fa-calendar-alt',
        'icon_color' => 'icc-8',
        'route'      => 'admin.events.index',
        'permit'     => 'event.show',
        'for_active' => 'admin.events',
        'sl'         => 8,
    ],
    [
        'title'      => 'HerCycle',
        'icon'       => 'fas fa-heart',
        'icon_color' => 'icc-9',
        'route'      => 'admin.hercycle.index',
        'permit'     => 'hercycle.show',
        'for_active' => 'admin.hercycle',
        'sl'         => 9,
    ],

    [
        'title'      => 'User Management',
        'icon'       => 'fas fa-users-cog',
        'icon_color' => 'icc-10',
        'sl'         => 10,
        'children'   => [
            [
                'icon'       => 'fas fa-users',
                'title'      => 'Users',
                'route'      => 'admin.users.index',
                'permit'     => 'user.view',
                'icon_color' => 'icc-11',
                'for_active' => 'admin.users',
            ],
            [
                'icon'       => 'fas fa-user-shield',
                'title'      => 'Roles',
                'route'      => 'admin.roles.index',
                'permit'     => 'role.view',
                'icon_color' => 'icc-12',
                'for_active' => 'admin.roles',
            ],
        ]
    ],
    [
        'permit'     => 'setting.edit',
        'title'      => 'Settings',
        'icon'       => 'fas fa-cog',
        'route'      => 'admin.settings.edit',
        'icon_color' => 'icc-12',
        'for_active' => 'admin.settings',
        'sl'         => 11,
    ],



];



