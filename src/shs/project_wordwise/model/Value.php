<?php

namespace shs\project_wordwise\model;

class Value extends Objectx {
    public const NA = [
        'CODE' => 0b0000_0000,
        'VALUE' => 'N/a',
        'DESCRIPTION' => 'Not applicable'
    ];
    public const WAITING = [
        'CODE' => 0b0000_0010,
        'VALUE' => 'WTG',
        'DESCRIPTION' => 'WAITING'
    ];
    public const READY = [
        'CODE' => 0b0000_0100,
        'VALUE' => 'RDY',
        'DESCRIPTION' => 'READY'
    ];
    public const ANOMYMOUS = [
        'CODE' => 0b0000_1000,
        'VALUE' => '[REM:] ANON',
        'DESCRIPTION' => 'ANONYMOUS'
    ];

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class,
            false
        );
    }
}
