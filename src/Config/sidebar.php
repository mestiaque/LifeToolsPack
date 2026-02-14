<?php

return [
    [
        'title' => 'Dashboard',
        'icon'  => 'fas fa-tachometer-alt',
        'route' => 'admin.dashboard',
        'icon_color' => 'text-encodex-secondary',
    ],
    [
        'title'    => 'Loans',
        'icon'     => 'fas fa-money-check-alt',
        'icon_color' => 'text-warning',
        'children' => [
            [
                'icon'   => 'fas fa-list',
                'title'  => 'All Loans',
                'route'  => 'admin.loans.index',
                'permit' => 'loan.show',
                'icon_color' => 'text-warning',
            ],
            [
                'icon'   => 'fas fa-user-lock',
                'title'  => 'Loan Users',
                'route'  => 'admin.loan-users.index',
                'permit' => 'loan_user.show',
                'icon_color' => 'text-warning',
            ],
        ]
    ],

    [
        'title'       => 'Daily Expense',
        'icon'        => 'fas fa-receipt',
        'icon_color'  => 'text-danger',
        'route'       => 'admin.daily-expenses.index',
        'permit'      => 'daily-expense.show',
    ],


    [
        'title'    => 'Drive',
        'icon'     => 'fas fa-hdd',
        'icon_color' => 'text-info',
        'route'  => 'admin.drive',
        'permit' => 'drive.view',
    ],
    [
        'title'    => 'Disk',
        'icon'     => 'fas fa-compact-disc',
        'icon_color' => 'text-info',
        'route'  => 'admin.disks.index',
        'permit' => 'disk.show',
    ],
    [
        'title'    => 'Gallery',
        'icon'     => 'fas fa-images',
        'icon_color' => 'text-primary',
        'route'  => 'admin.gallery.index',
        'permit' => 'gallery.action',
    ],
    [
        'title'    => 'Messages',
        'icon'     => 'fas fa-envelope',
        'icon_color' => 'text-info',
        'route'  => 'admin.messages.index',
        'permit' => 'message.show',
    ],
    [
        'title'    => 'Events',
        'icon'     => 'fas fa-calendar-alt',
        'icon_color' => 'text-info',
        'route'  => 'admin.events.index',
    ],
    [
        'title'    => 'User Management',
        'icon'     => 'fas fa-users-cog',
        'icon_color' => 'text-success',
        'children' => [
            [
                'icon'   => 'fas fa-users',
                'title'  => 'Users',
                'route'  => 'admin.users.index',
                'permit' => 'user.view',
                'icon_color' => 'text-success',
            ],
            [
                'icon'   => 'fas fa-user-shield',
                'title'  => 'Roles',
                'route'  => 'admin.roles.index',
                'permit' => 'role.view',
                'icon_color' => 'text-success',
            ],
        ]
    ],
    [
        'permit' => 'setting.edit',
        'title'  => 'Settings',
        'icon'   => 'fas fa-cog',
        'route'  => 'admin.settings.edit',
        'icon_color' => 'text-danger',
    ],



];



