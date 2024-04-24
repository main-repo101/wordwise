<?php
namespace shs\project_wordwise\model\questionnaire;
use shs\project_wordwise\model\Objectx;

//REM: Immutable class
final class Answer extends Objectx {

    public function __construct( String $id, array $answers ) {
        $this->init( $id, $answers );
    }

    private function init( String &$id, array &$answers ) {
        if( ( $id = parent::isBlank( $id, true ) ) != null &&
            !empty( $answers )
        ) {
            $this->ID = $id;
            $this->ANSWERS = $answers;
            return;
        }
        throw new \InvalidArgumentException( 'Both ID and/or an array of Answers should not be null.' );
    }

    #[\Override]
    public function hashCode(): String {
        $arrayXShallowCopy = $this->ANSWERS;
        sort( $arrayXShallowCopy );
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class .
            implode(', ', array_map( 'strtolower', $arrayXShallowCopy ) ),
            false
        );
    }

    #[\Override]
    public function toString(): String {
        return sprintf(
            "%s[ id='%s' numberOfPossibleAnswer=%d ]",
            parent::toString(),
            $this->ID,
            count( $this->ANSWERS )
        );
    }

    #[\Override]
    public function equals( ?Object $obj ): bool {
        if( parent::equals( $obj ) )
            return true;
        return ( $obj instanceof self ) &&
            $this->getAnswersSize() === $obj->getAnswersSize() &&
            parent::equalsArraysValue( $this->ANSWERS, $obj->ANSWERS );
    }

    public function getAnswersSize(): int {
        return \count( $this->ANSWERS );
    }

    public readonly String $ID;

    //REM: TODO-HERE; Make a checker for its content if valid, null, empty and so on. 
    //REM: And handle IndexOutOfBound Exception
    public readonly array $ANSWERS; 
}


// $ans = new Answer( '     id-2342  ', [''] );
// print( $ans->toString() . PHP_EOL );
