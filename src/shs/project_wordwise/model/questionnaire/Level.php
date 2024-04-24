<?php
namespace shs\project_wordwise\model\questionnaire;
use shs\project_wordwise\model\Objectx;

final class Level extends Objectx {

    private function __construct( 
        int $code, 
        String $value, 
        String $desc 
    ) {
        $this->CODE = $code;
        $this->VALUE = $value;
        $this->DESCRIPTION = $desc;
    }

    private static function init() {
        self::$EASY = new Level( 0b0000_0000, 'EASY_MODE', 'LEVEL EASY MODE' );
        self::$NORMAL = new Level( 0b0000_0001, 'NORMAL_MODE', 'LEVEL NORMAL MODE' );
        self::$HARD = new Level( 0b0000_0010, 'HARD_MODE', 'LEVEL HARD MODE' );
        self::$INSANE = new Level( 0b0000_0100, 'INSANE_MODE', 'LEVEL INSANE MODE' );
    }

    //REM: Problem with this; 
    //REM: $level = Level::asEasy(); //REM: Ok!
    //REM: print( $level->VALUE ); //REM: GOOD!
    //REM: print( $level::asInsane()->VALUE ); //REM: Oh no!
    //REM: Level::asEasy()::asHard()::asNormal()::asInsane() //REM: Oh no!
    public static function asEasy(): Level {
        if( !isset( self::$EASY ) )
            self::init();
        return self::$EASY;
    }

    public static function asNormal(): Level {
        if( !isset( self::$NORMAL ) )
            self::init();
        return self::$NORMAL;
    }

    public static function asHard(): Level {
        if( !isset( self::$HARD ) )
            self::init();
        return self::$HARD;
    }

    public static function asInsane(): Level {
        if( !isset( self::$INSANE ) )
            self::init();
        return self::$INSANE;
    }
    
    // public const EASY = [
    //     'CODE' => 0b0000_0001,
    //     'VALUE' => 'EASY_MODE',
    //     'DESCRIPTION' => 'LEVEL EASY MODE'
    // ];

    // public const NORMAL = [
    //     'CODE' => 0b0000_0010,
    //     'VALUE' => 'NORMAL_MODE',
    //     'DESCRIPTION' => 'LEVEL NORMAL MODE'
    // ];
    
    // public const HARD = [
    //     'CODE' => 0b0000_0100,
    //     'VALUE' => 'HARD_MODE',
    //     'DESCRIPTION' => 'LEVEL HARD MODE'
    // ];

    // public const INSANE = [
    //     'CODE' => 0b0000_1000,
    //     'VALUE' => 'INSANE_MODE',
    //     'DESCRIPTION' => 'LEVEL INSANE MODE'
    // ];

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class,
            false
        );
    }

    #[\Override]
    public function toString(): String {
        return sprintf(
            "%s[ code=%d, value='%s', desc='%s' ]",
            parent::toString(),
            $this->CODE, $this->VALUE, $this->DESCRIPTION
        );
    }

    #[\Override]
    public function equals( ?Object $obj ): bool {
        if( parent::equals( $obj ) )
            return true;
        return ( $obj instanceof self ) &&
            $this->CODE === $obj->CODE &&
            $this->VALUE === $obj->VALUE &&
            $this->DESCRIPTION === $obj->DESCRIPTION;
    }

    public static function getObjLevel( String|int $level ): Level {
        $levelStrToUpper = null;
        if( $level instanceof String )
            $levelStrToUpper = strtoupper( $level );
        $easy = self::asEasy();
        $normal = self::asNormal();
        $hard= self::asHard();
        $insane = self::asInsane();
        return match( $levelStrToUpper?? $level ) {
            $easy->VALUE /*| $easy->CODE | $easy->DESCRIPTION*/ => self::asEasy(), 
            $hard->VALUE /*| $hard->CODE | $hard->DESCRIPTION */=> self::asHard(), 
            $insane->VALUE /*| $insane->CODE | $insane->DESCRIPTION*/ => self::asInsane(),
            default => self::asNormal()
        };
    }

    public readonly int $CODE;
    public readonly String $VALUE;
    public readonly String $DESCRIPTION;

    private static Level $EASY;
    private static Level $NORMAL;
    private static Level $HARD;
    private static Level $INSANE;

    public const CODE = 0;
    public const VALUE = 1;
    public const DESCRIPTION = 2;

}


// print( Level::asHard()->CODE  . PHP_EOL );
// print( Level::asHard()->VALUE . PHP_EOL );
// print( Level::asHard()->DESCRIPTION. PHP_EOL );