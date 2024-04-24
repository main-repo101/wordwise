<?php

namespace shs\project_wordwise\database;

use shs\project_wordwise\model\Objectx;

final class DBCredential extends Objectx {

    public function __construct( 
        ?String $host/* = 'locahost'*/ = null, 
        ?String $username/*  = 'root'*/ = null,
        ?String $password/*  = ''*/ = null
    ) {
        $this->HOST = $host??'localhost';
        $this->USERNAME = $username??'root';
        $this->PASSWORD = $password??'';
    }

    #[\Override]
    public function toString(): String {
        return sprintf( 
            "mysql:host=%s;", 
            $this->HOST
        );
    }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            $this->HOST . $this->USERNAME . $this->PASSWORD,
            false
        );
    }

    public readonly String $HOST;
    public readonly String $USERNAME;
    public readonly String $PASSWORD;
}

// print( ( new DBCredential() )->toString() );