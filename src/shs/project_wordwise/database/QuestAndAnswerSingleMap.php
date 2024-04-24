<?php
declare( strict_types=1);

namespace shs\project_wordwise\database;
use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\Value;
use shs\project_wordwise\model\questionnaire\Quest;
use shs\project_wordwise\model\questionnaire\Answer;
use shs\project_wordwise\model\questionnaire\Level;
use shs\project_wordwise\model\io\JSONified;
use shs\project_wordwise\model\io\IO;

class QuestAndAnswerSingleMap extends Objectx {

    public function __construct() {
        self::setIdCounterWithFile( self::ID_COUNTER_FILENAME );
    }

    public function set(
        ?String $questType,
        String $question, 
        ?String $hint, 
        array $choices, 
        ?Level $level, 
        array $answers/*,
        IO $io*/
    ): bool {

        $id = $this->getCurrentId();
        $this->absorb( $id, $question, $choices, $questType, $level, $hint, $answers );
        // $this->QUEST = new Quest( $id, $question, $choices, $questType, $level, $hint );
        // $this->ANSWER = new Answer( $id, $answers);
        // $this->IO = $io;
        // $quest = new Quest( $id, $question, $choices, $questType, $level, $hint );
        // $answer = new Answer( $id, $answers);

        // $this->qAMap = [ 'QUEST' => $quest, 'ANSWER' => $answer ];

        //REM: TODO-HERE...
        
        // self::updateIdCounter();
        return true;
    }

    public function absorb( 
        String $id, 
        String $question, 
        array $choices, 
        ?String $questType, 
        ?Level $level, 
        ?String $hint,
        array $answers
    ): bool {
        return $this->absorbThem( 
            new Quest( $id, $question, $choices, $questType, $level, $hint ), 
            new Answer( $id, $answers)
        );
    }

    public function absorbThem( 
        Quest $quest, Answer $answer
    ): bool {
        if( isset($this->QUEST) || isset($this->ANSWER))
            return false;
        $this->QUEST = $quest;
        $this->ANSWER = $answer;
        return true;
    }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class .
            $this->QUEST->hashCode() .
            $this->ANSWER->hashCode(),
            false
        );
    }

    #[\Override]
    public function toString(): String {
        return sprintf( 
            "%s[ id='%s' ]",
            parent::toString(),
            ( $this->QUEST !== null )? $this->QUEST->ID : Value::NA['VALUE']
        );
    }

    public static function updateIdCounter(): void {
        self::setIdCounterWithFile( self::ID_COUNTER_FILENAME );
        self::updateIdCounterWithFile( self::ID_COUNTER_FILENAME );
    }

     //REM: TODO-HERE; ### needed Refactoring....
    protected static function setIdCounterWithFile( String $fileName ) {
        $data = (new JSONified( __PROJECT_PRIVATE_RESOURCE_DIR . $fileName ))->readAll();
        self::$id_counter = ( 
            ($data !== false)? ( 
                ( ( $x = $data['id_counter'] ) !== null )? 
                    ( ( $x <= 0 )? 1 : $x ) : 1
            ) : 1 
        );
    }

    //REM: TODO-HERE; ### needed Refactoring....
    protected static function updateIdCounterWithFile( String $fileName ): void {
        $counter_id_file = new JSONified( __PROJECT_PRIVATE_RESOURCE_DIR . $fileName );
        $counter_id_file->writeAll( '{ "id_counter": ' . ++self::$id_counter .' }' );
    }

    private function getCurrentId(): String {
        return sprintf( "qa-%04d", self::$id_counter );
    }

    // public function getMap(): ?array {
    //     return $this->qAMap;
    // }

    // public function getId(): ?String {
    //     $x = $this->qAMap['QUEST'];
    //     if( !( $x instanceof Quest ) )
    //         return null;
    //     return $x->ID;
    // }

    // public function isNotSet(): bool {
    //     return count( $this->qAMap ) === 0;
    // }

    private const ID_COUNTER_FILENAME = 'temp'. DIRECTORY_SEPARATOR . 'quest-and-answer.json';
    private static int $id_counter = 0;
    // private ?array $qAMap;
    // public readonly IO $IO;
    public readonly ?Quest $QUEST;
    public readonly ?Answer $ANSWER;
}