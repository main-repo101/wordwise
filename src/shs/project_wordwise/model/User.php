<?php

namespace shs\project_wordwise\model;

class User extends Entity {
    
    public function __construct( 
        String $id,
        ?bool $isAdmin = null,
        ?String $username = null,
        ?String $password = null,
        ?String $email = null 
    ) {

        parent::__construct( $id );
        $this->username = $username?? Value::NA['VALUE'];
        $this->password = $password?? Value::NA['VALUE'];
        $this->email = $email?? Value::NA['VALUE'];
        $this->isAdmin = $isAdmin?? false;
        $this->isActive = false;
    }

    public function setUsername( ?String $username ): void {
        parent::verifyStr( $username, $this->username );
    }

    public function setPassword( ?String $password ): void {
        if( ($pwd = parent::isBlank( $password, true, false )) != null )
            $this->password = password_hash( $pwd, PASSWORD_ARGON2ID );
    }

    public function setEmail( ?String $email ): void {
        parent::verifyStr( $email, $this->email );
    }

    public function getUsername(): String {
        return $this->username;
    }

    public function getPassword(): String {
        return $this->password;
    }

    public function getEmail(): String {
        return $this->email;
    }

    public function setActive( bool $isActive ) : void {
        $this->isActive = $isActive;
    }

    #[\Override]
    public function toString(): String {
        return preg_replace(
            '/ *\]$/i', 
            sprintf( 
                ", username='%s', email='%s', pwd='%s' ]",
                $this->username, $this->email, $this->password 
            ), 
            parent::toString() 
        );
    }
    
    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            parent::hashCode() .
            strtoupper( $this->username ) .
            strtoupper( $this->email ),
            false
        );
    }

    public function isAdmin(): bool {
        return $this->isAdmin;
    }

    public function isActive(): bool {
        return $this->isActive;
    }

    public function toggleActive( ?bool $isActive = null ): bool {
        return $this->isActive = ( $isActive )?? !$this->isActive;
    }

    #[\Override]
    public function equals( ?Objectx $obj ): bool {
        return ( $obj instanceof self ) &&
            strtoupper( $this->username ) === strtoupper( $obj->getUsername() ) &&
            strtoupper( $this->email ) === strtoupper( $obj->getEmail() );
    }

    #[\Override]
    public function updateSession(): Self {
        $_SESSION['user_info']['username'] = $this->username;
        $_SESSION['user_info']['email'] = $this->email;
        $_SESSION['user_info']['is_admin'] = $this->isAdmin;
        $_SESSION['user_info']['is_logged_in'] = $this->isActive;
        return parent::updateSession();
    }

    #[\Override]
    public function decodeSession(): bool {
        if( !isset( $_SESSION['user_info']['username'] ) )
            return false;
        $this->setUsername( $_SESSION['user_info']['username'] );
        $this->setEmail( $_SESSION['user_info']['email'] );
        $this->isAdmin = $_SESSION['user_info']['is_admin']?? false;
        $this->isActive = $_SESSION['user_info']['is_logged_in']?? false;
        return parent::decodeSession();
    }

    

    private String $username;
    private String $password;
    private String $email;

    //REM: TODO-HERE
    private bool $isAdmin;
    private bool $isActive;
    
}

// $user = new User( 'id-101' );
// print( $user->toString() . PHP_EOL );
// print( $user->isActive() . PHP_EOL );
// print( $user->toggleActive() . PHP_EOL );
// print( $user->isActive() . PHP_EOL );
// print( $user->toggleActive() . PHP_EOL );
// print( $user->isActive() . PHP_EOL );

