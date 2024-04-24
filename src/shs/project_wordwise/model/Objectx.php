<?php

namespace shs\project_wordwise\model;

/**
 * 'shs\project_wordwise\model\Objectx' ~ Mother of all custom classes and abstract classes being implemented here.
 */
abstract class Objectx {


    public abstract function hashCode(): string;

    public function __construct() {
        //REM: Ignore...
    }

    public function equals( ?Objectx $obj ): bool {
        return $obj != null && $obj === $this;
    }
    
    /**
     * 
     * @param array $arrayXPrime to be compared to the second param
     * @param array $arrayYPrime to be compare to the first param
     * @param bool $isUnique if TRUE it removes duplicated value, otherwise if set to FALSE they remained
     */
    public static function equalsArraysValue( 
        array $arrayXPrime, 
        array $arrayYPrime, 
        bool $isUnique = false 
    ): bool {
        //REM: Normalize the case of values and remove duplicates
        $arrayXNormalize = array_map( 
            fn($value) => strtolower( $value ), 
            $isUnique? array_unique($arrayXPrime) : $arrayXPrime 
        );

        $arrayYNormalize = array_map(
            'strtolower', 
            $isUnique? array_unique($arrayYPrime) : $arrayYPrime 
        );
    
        //REM: Sort arrays to ensure the same order
        sort($arrayXNormalize);
        sort($arrayYNormalize);
    
        //REM: Compare arrays
        return $arrayXNormalize === $arrayYNormalize; //REM: On arrays with '=== operator' it implement early-return strategy
    }

    public static function compareArraysV2(
        array &$arrayXPrime, array &$arrayYPrime
    ) : bool {
        return self::compareArrays( 
            $arrayXPrime, count( $arrayXPrime ),
            $arrayYPrime, count( $arrayYPrime ),
            0, 0
        );
    }

    //REM: dynamic programming with recursive and memoization...
    public static function compareArrays(
        array &$arrayXPrime, int $arrayXPrimeLen, 
        array &$arrayYPrime, int $arrayYPrimeLen,
        int $indexX, int $indexY, 
        array &$memo = []
    ): bool {
        //REM: Check if we have already computed the result for these indices
        if (isset($memo[$indexX][$indexY]))
            return $memo[$indexX][$indexY];
    
        //REM: Base case: both arrays have reached the end
        if ($indexX == $arrayXPrimeLen && $indexY == $arrayYPrimeLen)
            return true;
    
        //REM: If only one array has reached the end
        if ($indexX == $arrayXPrimeLen || $indexY == $arrayYPrimeLen)
            return false;
    
        //REM: If the current elements are not equal, arrays are not equal
        if ($arrayXPrime[$indexX] !== $arrayYPrime[$indexY])
            return $memo[$indexX][$indexY] = false;
    
        //REM: Move to the next elements in both arrays
        return $memo[$indexX][$indexY] = self::compareArrays(
            $arrayXPrime, $arrayXPrimeLen, 
            $arrayYPrime, $arrayYPrimeLen,
            $indexX + 1, $indexY + 1, 
            $memo
        );
    }
    
    public static function mustBe( 
        null | string | object $obj, 
        \Closure $fn,
         bool $isReturnStr = false,
         bool $isTrim = true
    ): null | string | bool {
        return $fn( $obj, $isReturnStr, $isTrim );
    }
    
    /**
     * 
     * @return ?String return null if the String is empty, otherwise return the String
     * @return bool return TRUE if not blank, otherwise FALSE
     */
    public static function isBlank(
        ?string $str, 
        bool $isReturnStr = false,
        bool $isTrim = true
    ): null | string | bool {
        // return Objectx::mustBe($str, function($subject, $isReturnStr, $isTrim ): null | string | bool {
        //     $trimStr = '';
        //     $isBlank = $subject == null || empty($trimStr = trim((string)$subject));
        //     if (!$isReturnStr)
        //         return $isBlank;
        //     return $isTrim? $trimStr : $subject;
        // }, $isReturnStr, $isTrim );
        return Objectx::mustBe($str, function($subject, $isReturnStr, $isTrim ): null | string | bool {
            $trimStr = trim( (string)$subject );
            if ( !$isReturnStr )
                return empty( $trimStr );
            return $isTrim ? $trimStr : ( ($trimStr == null)? null : $subject  );
        }, $isReturnStr, $isTrim );
    }
    
    public function toString(): String {
        return sprintf( "%s@%s", $this::class, $this->hashCode() );
    }
    
    protected const HASH_ALGO_SHA_256 = 'sha256';
    //REM: TODO-HERE; more hash algorithm here....

    public const X = 1;
    
}
// require_once( '/User.php' );

// $entity = new User( '  id-101' );
// $entity->setPassword( '  wow123  sadfa ' );
// $entity->setFirstName( 'Ang' );
// $entity1 = new User( '  id-101' );
// $entity1->setPassword( '  wow123  sadfa ' );
// $entity1->setFirstName( 'AnG   ' );

// print( $entity->toString() . "\n" );
// print( $entity1->toString() . "\n" );
// print( $entity->equals( $entity1 ) . "\n" );


// print( Objectx::equalsArraysValue( ['2z' => -1, 'a' => 2], ['22a' => 2, 3 => -1] ) . PHP_EOL );
// print( Objectx::equalsArraysValue( ['2z' => -1, 'a' => 2, '3a' => '-12' ], ['22a' => 2, '3' => -1, '3a' => -1 ] ) . PHP_EOL );

// $a = [1, 2];
// $b = [2, 1];

// sort( $a );
// sort( $b );

// print( 
//     Objectx::compareArrays(
//         $a, 2, $b, 2,
//         0, 0
//     ) . PHP_EOL
// );

// print( 
//     Objectx::compareArraysV2(
//         $a, $b
//     ) . PHP_EOL
// );