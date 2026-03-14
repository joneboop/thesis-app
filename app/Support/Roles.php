<?php

namespace App\Support;

final class Roles
{
    public const OWNER = 'owner';
    public const ADMIN = 'admin';
    public const MEMBER = 'member';
    public const READONLY = 'readonly';

    public const ALL = [
        self::OWNER,
        self::ADMIN,
        self::MEMBER,
        self::READONLY,
    ];

    public const RANK = [
        self::READONLY => 10,
        self::MEMBER   => 20,
        self::ADMIN    => 30,
        self::OWNER    => 40,
    ];
}