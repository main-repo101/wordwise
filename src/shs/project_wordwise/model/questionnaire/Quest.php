<?php
namespace shs\project_wordwise\model\questionnaire;


use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\Value;

//REM: Warning this is IMMUTABLE, and a realy strict one...
final class Quest extends Objectx {

    public function __construct( 
        String $id,
        String $question,
        array $choices = [],
        ?String $questType = null,
        ?Level $level = null,
        ?String $hint = null,
    ) {
        $this->init( 
            $id,
            $question,
            $choices,
            $questType,
            $level,
            $hint
        );
    }

    private function init( 
        String $id,
        String $question,
        array $choices = [],
        ?String $questType = null,
        ?Level $level = null,
        ?String $hint = null
    ) {
        // $this->ID = trim( 
        //     self::PREFIX_ID . self::DELIMITER_ID . 
        //     strtoupper( ( !parent::isBlank( $id ) )? $id : Value::ANOMYMOUS['DESCRIPTION'] ) 
        // );

        $this->ID = $id;
        if( !empty( $quest = parent::isBlank( $question, true ) ) &&
            !empty( $choices )
        ) {
            $this->QUESTION = $quest;
            $this->CHOICES = $choices;
            $this->QUEST_TYPE = match(trim($questType ?? '')) {
                QuestType::POST => QuestType::POST,
                default => QuestType::PRE
            };
            $this->LEVEL = $level?? Level::asEasy();
            $this->HINT = $hint?? Value::NA['VALUE'];
            return;
        }

        throw new \InvalidArgumentException( 'Both Question and Choices should not be empty nor null' );
    }

    public function getLevel( ?int $option = Level::VALUE ): int | String {
        return match( $option  ) {
            Level::CODE => $this->LEVEL->CODE,
            Level::DESCRIPTION => $this->LEVEL->DESCRIPTION,
            default => $this->LEVEL->VALUE
        };
    }

    #[\Override]
    //REM: This is for checking if we have the same content QnA.
    //REM: Yes, indeed we might suffer from a hash collision, but
    //REM: this is only for a quick fix. 
    public function hashCode(): String {
        $arrayXShallowCopy = $this->CHOICES;
        sort( $arrayXShallowCopy );
        return hash(
            parent::HASH_ALGO_SHA_256,
            $this::class .
            $this->QUEST_TYPE .
            strtolower( $this->QUESTION ) .
            implode(', ', array_map( 'strtolower', $arrayXShallowCopy ) ),
            // $this->ID, //REM: Yap, but we know what we're doing right now.
            false
        );
    }

    #[\Override]
    public function toString(): String {
        return sprintf( 
            "%s[ id='%s', level='%s' ]",
            parent::toString(),
            $this->ID,
            $this->LEVEL->VALUE
        );
    }

    public function getChoicesSize(): int {
        return \count( $this->CHOICES );
    }

    #[\Override]
    public function equals( ?Object $obj ): bool {
        if( parent::equals( $obj ) )
            return true;
        return ( $obj instanceof self ) &&
            $this->getChoicesSize() === $obj->getChoicesSize() &&
            $this->ID === $obj->ID &&
            $this->LEVEL->equals( $obj->LEVEL ) &&
            \strtoupper( $this->QUESTION ) === \strtoupper( $obj->QUESTION ) &&
            parent::equalsArraysValue( $this->CHOICES, $obj->CHOICES );
    }

    // public function getPostFixId(): String {
    //     $arrays = explode( self::DELIMITER_ID, $this->ID );
    //     return end( $arrays );
    // }

    // private const PREFIX_ID = 'quest';
    // private const DELIMITER_ID = '-';

    public readonly String $QUEST_TYPE;
    public readonly Level $LEVEL;
    public readonly String $HINT; //REM: TODO-HERE; Make it secure...
    public readonly String $QUESTION;
    public readonly array $CHOICES; //REM: TODO-HERE; Make a checker for its content if valid, null, empty and so on. And handle IndexOutOfBound Exception
    public readonly String $ID;
}

// $quest = new Quest( 'ok  ', '    asd', ['a', 1, 2 ], Level::asInsane() );
// print( $quest->toString() .PHP_EOL );

// $quest2 = new Quest( 'ok', "  asd", [2, 1, 'A'], Level::asInsane() );
// print( $quest2->toString() .PHP_EOL );

// print( "::" . $quest2->equals( $quest ) .PHP_EOL );
// print( "::" . $quest2->HINT .PHP_EOL );
// print( "::" . $quest->HINT .PHP_EOL );
// print( "::" . $quest2->QUESTION .PHP_EOL );
// print( "::" . $quest->QUESTION .PHP_EOL );
// print( "::" . $quest2->getChoicesSize() .PHP_EOL );
// print( "::" . $quest->getChoicesSize() .PHP_EOL );


