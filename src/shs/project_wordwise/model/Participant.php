<?php
namespace shs\project_wordwise\model;

use shs\project_wordwise\model\io\IO;
use shs\project_wordwise\model\io\ISerializable;

class Participant extends User implements ISerializable {

    /**
     * Note: The participant Id might be overwriten by the DBParticipantManagement
     * if the said participant is not yet existed in the db.
     */
    public function __construct( 
        String $id,
        bool $hasPrefixId = false,
        ?String $firstName = null,
        ?String $middleName = null,
        ?String $lastName = null,
        ?Score $score = null,
    ) {
        
        parent::__construct( 
            $id,
            false
        );
        
        // self::setIdCounterFile( self::CACHE_ID_COUNTER_FILENAME );
        if( $hasPrefixId )
            $this->setId( self::PARTICIPANT_PREFIX_ID . '-' . parent::getId() );
        parent::setFirstName( $firstName );
        parent::setMiddleName( $middleName );
        parent::setLastName( $lastName );
        $this->score = $score?? new Score( parent::getId() );
    }

    // public static function stopIdCounter( bool $isStopIdCounter  ) {
    //     return parent::setStopIdCounter( $isStopIdCounter, self::CACHE_ID_COUNTER_FILENAME );
    // }

    // #[\Override]
    // public static function updateIdCounter(): void {
    //     parent::updateIdCounter(); //REM: update Entity id... or ( User id if impl existed. )
    //     //REM: update participant id...
    //     self::setIdCounterFile( self::CACHE_ID_COUNTER_FILENAME );
    //     self::updateIdCounterFile( self::CACHE_ID_COUNTER_FILENAME );
    // }

    #[\Override]
    public function toString(): String {
        return preg_replace(
            '/ *\]$/i', 
            sprintf( 
                ", %s ]",
                $this->score->toString()
            ), 
            parent::toString() 
        );
    }

    //REM: WARNING; not yet tested...
    #[\Override]
    public function decode( IO $io ):  bool {
        $data = $io->readAll();
        if ($data !== false && is_array($data)) {
            parent::decode( $io );
            //REM: Check if the array is 2-dimensional
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data as $item) {
                    if ($item['id'] === $this->getId() ) {
                        $this->score->setTotalItems( $data['score']['total_items']?? 0 );
                        $this->score->setPoints( $data['score']['points']?? 0 );
                        return true;
                    }
                }
            } else {
                //REM: Handle if array is not 2-dimensional
                if ( $data['id'] === $this->getId() ) {
                    $this->score->setTotalItems( $data['score']['total_items']?? 0 );
                    $this->score->setPoints( $data['score']['points']?? 0 );
                    return true;
                }
            }
        }
        return false;
    }

    #[\Override]
    public function encode( int $encodeAs = ISerializable::JSON ): String | false {
        return match( $encodeAs ) {
            ISerializable::YAML => "yum yum",
            default => json_encode(
                [
                    "id" => parent::getId(),
                    "full_name" => parent::getName(),
                    "age" => parent::getAge(),
                    "score" => [
                        "points" => $this->score->getPoints(),
                        "rate" => $this->score->getRate(),
                        "total_items" => $this->score->getTotalItems(),
                        "total_items_points" => $this->score->getTotalItemsPoints()
                    ]
                ]
            )
        };
    }

    #[\Override]
    public function updateSession(): Self {
        $_SESSION['user_info']['score_info'] = [
            'participant_id' => $this->getId(), 
            'rate' => $this->score->getRate(), 
            'points' => $this->score->getPoints(), 
            'total_items' => $this->score->getTotalItems(), 
            'each_item_points' => $this->score->getEachItemPoints(),
            'total_items_points' => $this->score->getTotalItemsPoints(),
        ];
        return parent::updateSession();
    }

    #[\Override]
    public function decodeSession(): bool {
        if( !isset( $_SESSION['user_info']['score']['participant_id'] ) )
            return false;
        $this->score->setPoints( $_SESSION['user_info']['score']['points']?? 0 );
        $this->score->setTotalItems( $_SESSION['user_info']['score']['total_items']?? 0 );
        $this->score->setEachItemPoints( $_SESSION['user_info']['score']['each_item_points']?? 1 );
        return parent::decodeSession();
    }

    public const PARTICIPANT_PREFIX_ID = 'participant';
    // protected const CACHE_ID_COUNTER_FILENAME = 'temp'. DIRECTORY_SEPARATOR . 'participant.json' ;
    //REM: TODO-HERE; refactor it. Feels like it is really wrong implementation, but we have to try.

    public readonly Score $score;
}

// $participant = new Participant( 'id-1011', true, 'ok', "wow", 'adf' );
// $participant->setPassword( 'a ' );
// print( $participant->toString() . PHP_EOL );
// print( $participant->encode() . PHP_EOL  );

// $participant->score->setTotalItems( 20 );
// $participant->score->setItemPoints( 7 );
// $participant->score->update( 70 );


// print( $participant->toString() . PHP_EOL );
// print( $participant->encode() . PHP_EOL  );

// print( $participant->score->toString() . PHP_EOL  );
// print( $participant->score->getTotalItemsPoints()  . PHP_EOL );


//REM: DB: word_wise,
//REM: TABLE: participant
//REM: FIELDS: id, first_name, middle_name, last_name, gender, age, 