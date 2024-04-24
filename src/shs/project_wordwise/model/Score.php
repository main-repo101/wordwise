<?php
namespace shs\project_wordwise\model;

class Score extends Objectx {

    public function __construct( ?String $id, int $totalItems = 0, ?int $eachItemPoints = null ) {
        $this->init( 
            $id,
            $totalItems,
            $eachItemPoints
        );
    }

    private function init( ?String $id, int $totalItems, ?int $eachItemPoints ) {
        $strVal = parent::isBlank( $id, true );
        $this->id = ($strVal == null)? Value::ANOMYMOUS['VALUE'] : $strVal;
        // Entity::verifyStr( ($id != null)? $id : Value::ANOMYMOUS['VALUE'], $this->id );
        $this->setTotalItems( $totalItems );
        $this->setItemPoints( $eachItemPoints );
        $this->points = 0;
    }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            $this->id /*. $this->totalItems . $this->points . $this->getRate() */,
            false
        );
    }

    #[\Override]
    public function equals( ?Object $obj ): bool {
        if( parent::equals( $obj ) ) 
            return true;
        return ( $obj instanceof self ) && 
            $this->totalItems === $obj->totalItems &&
            $this->points === $obj->points &&
            bccomp( $this->getRate(), $obj->getRate(), 10 ) === 0;
    }

    #[\Override]
    public function toString(): String {
        return sprintf( "%s@%s[ id='%s', points=%d, totalItems=%d, eachItemPoints=%d, rating=%s ]", 
            self::class, 
            $this->hashCode(),
            $this->id,
            $this->points,
            $this->totalItems,
            $this->eachItemPoints,
            $this->getRateAsStr()
        );
    }

    public function getRateAsStr(): String {
        $rating = $this->getRate();
        $ratingFmt = "%.2f%%";
        $tolerance = 0.0001;
        if( abs($rating - floor($rating)) < $tolerance ) //REM: ( 91.01: 0.01 < 0.0001 == false ); ( 91.00001: 0.00001 < 0.0001 == true )
            $ratingFmt = "%d%%";

        return sprintf( $ratingFmt, $rating );
    }

    public function setItemPoints( ?int $eachItemPoints ): void {
        if( $eachItemPoints != null && $eachItemPoints > self::DEFAULT_ITEM_POINTS )
            $this->eachItemPoints = $eachItemPoints;
    }

    public function setTotalItems( int $totalItems ): void {
        if( $totalItems > self::DEFAULT_TOTAL_ITEMS ) 
            $this->totalItems = $totalItems;
    }

    public function setEachItemPoints( int $eachItemPoints ): void {
        if( $eachItemPoints > 0 )
            $this->eachItemPoints = $eachItemPoints;
    }

    /**
     * Decrease number of items
     */
    public function pop( int $numberItem = 1  ) {
        $this->setTotalItems( max( 0, $this->totalItems - $numberItem ) );
    }

    public function update( int $points/*, ?int $eachItemPoints = null*/ ) {
        //REM: TODO-HERE; more warning logical operations here...
        //REM: We should be cautious if the accumulated points reach the maximum limit.
        //REM: Additionally, it's advisable to alert if the inputted points exceed the maximum threshold.
        //REM: Furthermore, we should be mindful of any decrease or reduction in accumulated points, 
        //REM: especially if they reach zero.
        if ( $this->getRate() !== 100 || $points < 0 ) {
            $maxPoints = $this->getTotalItemsPoints();
            $newPoints = $this->points + $points;

            //REM: to constraint points from getting negative value 
            //REM: or greater than the overall points.
            $this->points = max(0, min($newPoints, $maxPoints)); 
        }
    }

    public function setPoints( int $point ) {
        if( $point > 0 )
            $this->points = $point;
    }

    // private function setRate( int | float $rate ): void {
    //     $this->rating = $rate;
    // }

    // public function getRate(): int | float {
    //     return $this->rating;
    // }

    public function getTotalItemsPoints(): int {
        return $this->totalItems * $this->eachItemPoints;
    }

    public function getRate(): int | float {
        $currentPoints = $this->points;
        $totalItemsPoints = $this->getTotalItemsPoints();
        $scalar = 100;
        try {
            $result = ($currentPoints/$totalItemsPoints) * $scalar;
            return $result;
        } catch( \DivisionByZeroError $exception ) {
            //REM: Ignore exception
            return 0;
        }
    }

    public function getPoints(): int {
        return $this->points;
    }

    public function getTotalItems(): int {
        return $this->totalItems;
    }

    public function getEachItemPoints(): ?int {
        return $this->eachItemPoints;
    }

    public const DEFAULT_TOTAL_ITEMS = 0;
    public const DEFAULT_ITEM_POINTS = 1;

    public readonly String $id;
    private int $totalItems = self::DEFAULT_TOTAL_ITEMS;
    private ?int $eachItemPoints = self::DEFAULT_ITEM_POINTS;
    private int $points = 0;
}

// $score = new Score( '123abc1', 1, 1 );
// print( "::: " . $score->toString() . PHP_EOL );
// $score->update( 1 );
// print( "::: " . $score->toString() . PHP_EOL );
// $score = new Score( '123abc2', 1, 1 );
// print( "::: " . $score->toString() . PHP_EOL );
// $score->update( 1 );
// print( "::: " . $score->toString() . PHP_EOL );