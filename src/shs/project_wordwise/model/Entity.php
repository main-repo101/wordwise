<?php

namespace shs\project_wordwise\model;
use shs\project_wordwise\model\io\ISerializable;
use shs\project_wordwise\model\io\IO;
use shs\project_wordwise\model\io\JSONified;

class Entity extends Objectx implements ISerializable {

    public function __construct( 
        ?String $id, 
        ?String $firstName = null, 
        ?String $middleName = null, 
        ?String $lastName = null, 
        int $age = 0
    ) {
        parent::__construct();
        $this->init( 
            $id,
            $firstName,
            $middleName,
            $lastName,
            $age
        );
    }

    private function init( 
        ?String $id, 
        ?String $firstName,
        ?String $middleName,
        ?String $lastName,
        int $age
    ) {
        if( $id != null && !empty( $id = trim( $id) ) ) {
            self::setIdCounterFile( self::CACHE_ID_COUNTER_FILENAME );
            $this->id = $id === Value::NA['VALUE']? self::getIdCounter() : $id;
            $this->firstName = $firstName?? Value::NA['VALUE'];
            $this->middleName = $middleName?? Value::NA['VALUE'];
            $this->lastName = $lastName?? Value::NA['VALUE'];
            $this->age = $age;
            return;
        }
        throw new \RuntimeException( 
            sprintf( "%s '\$id' should not be null nor empty", self::class ) 
        );
    }

    #[\Override]
    public function toString() : String {
        return sprintf( "%s@%s[ id='%s', name='%s']", 
            $this::class, 
            $this->hashCode(),
            $this->getId(),
            $this->getName()
        );
    }
    public function setId( ?String $id ): void {
        $this::verifyStr( $id, $this->id );
    }
    public function setFirstName( ?String $firstName ): void {
        $this::verifyStr( $firstName, $this->firstName );
    }
    public function setMiddleName( ?String $middleName ): void {
        $this::verifyStr( $middleName, $this->middleName );
    }
    public function setLastName( ?String $lastName ): void {
        $this::verifyStr( $lastName, $this->lastName );
    }
    public function setAge( int $age ) {
        if( $age < 0 )
            return;
    }

    public function getFirstName(): String {
        return $this->firstName;
    }
    public function getMiddleName(): String {
        return $this->middleName;
    }
    public function getLastName(): String {
        return $this->lastName;
    }

    protected function getIdCounter(): String {
        return sprintf( "%04d", self::$id_counter );
    }


    //REM: TODO-HERE; refactor it.
    public function getId(): string {
        // // return $this->id === Value::NA['VALUE'] ? $this->getIdCounter() : $this->id;
        // if( $this->id === Value::NA['VALUE'] )
        //     return $this->getIdCounter();
        
        // $pattern = '/^(.*?-)\d+$/'; //REM: e.q: prefix_name-0001 ==> prefix_name-
        // $replacement = '${1}';
        // $this->setId( preg_replace($pattern, $replacement, $this->id ) . $this->getIdCounter() );
        return $this->id;
    }

    public static function setIdCounterFile( String $fileName ) {
        //REM: TODO-HERE; ### needed Refactoring....
        $data = (new JSONified( __PROJECT_PRIVATE_RESOURCE_DIR . $fileName ))->readAll();
        self::$id_counter =  ( 
            ($data !== false)? ( 
                ( ( $x = $data['id_counter'] ) !== null )? 
                    ( ( $x <= 0 )? 1 : $x ) : 1
            ) : 1 
        );
    }

    public static function updateIdCounter(): void {
        self::setIdCounterFile( self::CACHE_ID_COUNTER_FILENAME );
        self::updateIdCounterFile( self::CACHE_ID_COUNTER_FILENAME );
    }

    //REM: TODO-HERE; ### needed Refactoring....
    protected static function updateIdCounterFile( String $fileName ): void {
        $counter_id_file = new JSONified( __PROJECT_PRIVATE_RESOURCE_DIR . $fileName );
        $counter_id_file->writeAll( 
            '{ "id_counter": ' . 
                ( (self::$is_stop_id_counter)? self::$id_counter : ++self::$id_counter ) .' }' 
        );
    }

    public function getName( int $order = self::NAME_PRE_FIX ) : String {
        $fn = ucfirst( strtolower( $this->firstName ) );
        $mn = strtolower( $this->middleName ) === strtolower( Value::NA['VALUE'] )? $this->middleName
                : strtoupper( substr( $this->middleName, 0, 1 ) ) . '.';
        $ln = ucfirst( strtolower( $this->lastName ) );

        switch( $order ) {
            case self::NAME_POST_FIX:
                return sprintf( "%s, %s %s", $fn, $mn, $ln );
                break;
            default:
                return sprintf( "%s %s %s", $fn, $mn, $ln );
        }
    }



    public function getAge(): int {
        return $this->age;
    }

    #[\Override]
    public function hashCode(): String {
        return hash( 
            parent::HASH_ALGO_SHA_256, 
            $this->id /*. 
            $this->getName() .
            $this->age */,
            false
        );
    }

    #[\Override]
    public function equals( ?Objectx $obj ): bool {
        if( parent::equals( $obj ) )
            return true;
        if( !( $obj instanceof self) )
            return false;
        return $obj->id === $this->id && 
            $obj->getName() === $this->getName();
    }

    /**
     * 
     * @param ?String $str the source
     * @param String &$inOutStr the dist
     */
    public static function verifyStr( ?String $str, String &$inOutStr, bool $isTrim = true ): void {
        $e = parent::isBlank( $str, true, $isTrim );
        if( $e != null && $e !== $inOutStr )
            $inOutStr = $e;
    }

    #[\Override]
    public function decode( IO $io ):  bool {
        $data = $io->readAll();
        if ($data !== false && is_array($data)) {
            //REM: Check if the array is 2-dimensional
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data as $item) {
                    if ($item['id'] === $this->id) {
                        $this->setFirstName($item['first_name'] );
                        $this->setMiddleName($item['middle_name'] );
                        $this->setLastName($item['last_name'] );
                        $this->setAge($item['age']);
                        return true;
                    }
                }
            } else {
                //REM: Handle if array is not 2-dimensional
                if ($data['id'] === $this->id) {
                    $this->setFirstName($data['first_name'] );
                    $this->setMiddleName($data['middle_name'] );
                    $this->setLastName($data['last_name'] );
                    $this->setAge($data['age'] );
                    return true;
                }
            }
        }
        return false;
    }

    #[\Override]
    public function encode( int $encodeAs = ISerializable::JSON ): String | false {
        return match( $encodeAs ) {
            ISerializable::YAML => "NotSupportedException...", /*REM: TODO-HERE: */
            default => json_encode(
                [
                    "id" => $this->id,
                    "first_name" => $this->firstName,
                    "middle_name" => $this->middleName,
                    "last_name" => $this->lastName,
                    "age" => $this->age
                ]
            )
        };
    }

    /**
     * 
     * ```
     * $is_stop_id_counter = false;
     * $id_counter = 1;
     * setStopIdCounter( false ) //REM: $is_stop_id_counter = false; $id_counter = 1 + 1 = 2;
     * setStopIdCounter( true ) //REM: $is_stop_id_counter = true; $id_counter = 0 + 0 = 2; 
     * setStopIdCounter( false ) //REM: $is_stop_id_counter = false; $id_counter = 2 + 1 = 3;
     * ```
     * @param int $isStopIdCounter '-1' normal execution no anomaly mutation, this 
     * will not autmatically increment the said $id_counter.
     * @param bool $isStopIdCounter TRUE don't increment $id_counter, 
     * But if it is TRUE then later on it set to FALSE, then, it will
     * increment automatically $id_counter by 1.
     * 
     * #### REM: Use this with caution; it is usually use for multiple creation of same ID, such as;
     * ```
     * Participant::setStopIdCounter( true ); 
     * 
     * $Participant = new Participant( Value::NA['Value'] );
     * $Participant->setUsername( 'A' );
     * 
     * $db = new DBParticipantManagement();
     * 
     * $db->switchTableByQuestType( QuestType::PRE );
     * $db->create( $entity ); //REM: will not auto-increment id even it is created succesfully
     * 
     * $db->switchTableByQuestType( QuestType::POST );
     * $db->create( $entity ); //REM: will not auto-increment id even it is created succesfully
     * 
     * Participant::setStopIdCounter( false ); //REM: don't ever forget this
     * 
     * ```
     * 
     */
    //REM: TODO-HERE; refactor it.
    public static function setStopIdCounter( bool /*| int */ $isStopIdCounter /* = -1 */, String $filename ): void {
        // if( $isStopIdCounter !== -1 && $isStopIdCounter === true) {
        //     self::$is_stop_id_counter = true;
        // } elseif( $isStopIdCounter === -1 ) { //REM: do normal id incrementation 
        //     self::$is_stop_id_counter = false;
        // } elseif( self::$is_stop_id_counter !== false ) {
        //     self::$is_stop_id_counter = false;
        //     self::updateIdCounter();
        // }
        if( !isset( self::$is_stop_id_counter ) || $isStopIdCounter ) /*REM: normal execution*/
            self::$is_stop_id_counter = ( self::$is_stop_id_counter?? false ) ^ ( $isStopIdCounter );
        elseif( self::$is_stop_id_counter ) { //REM: if TRUE then set to FALSE and increment id by 1
            self::$is_stop_id_counter = false;
            self::updateIdCounterFile( $filename );
        }
    }

    public static function stopIdCounter( bool $isStopIdCounter  ) {
        return self::setStopIdCounter( $isStopIdCounter, self::CACHE_ID_COUNTER_FILENAME );
    }

    public function decodeSession(): bool {
        if( !isset( $_SESSION['user_info']['participant_id'] ) )
            return false;
        $this->setId( $_SESSION['user_info']['participant_id'] );
        $this->setFirstName( $_SESSION['user_info']['first_name'] );
        $this->setMiddleName( $_SESSION['user_info']['middle_name'] );
        $this->setLastName( $_SESSION['user_info']['last_name'] );
        $this->setAge( $_SESSION['user_info']['age'] );
        return true;
    }

    public function updateSession(): Self {
        $_SESSION['user_info']['participant_id'] = $this->id;
        $_SESSION['user_info']['first_name'] = $this->firstName;
        $_SESSION['user_info']['middle_name'] = $this->middleName;
        $_SESSION['user_info']['last_name'] = $this->lastName;
        $_SESSION['user_info']['age'] = $this->age;
        return $this;
    }

    private const NAME_POST_FIX = 0b0000_0000;
    private const NAME_PRE_FIX = 0b0000_0001;

    protected const CACHE_ID_COUNTER_FILENAME = 'temp'. DIRECTORY_SEPARATOR . 'entity.json' ;
    protected static ?bool $is_stop_id_counter;
    protected static int $id_counter; 
    private String $id;
    private String $firstName;
    private String $middleName;
    private String $lastName;
    private int $age;

    // public static int $count = 0;

    //REM: TODO-HERE;
    //private Gender $gender;
}
// // print( ":" . realPath('../private/test/j.json') . ":" . PHP_EOL );
// $entity = new Entity( 'id-1012', 'asdf' );
// print( $entity->toString() . PHP_EOL );
// $encoded = $entity->encode();
// print(  $encoded . PHP_EOL );
// print( __PROJECT_ROOT_DIR . PHP_EOL );
// print( __PROJECT_RESOURCE_DIR . PHP_EOL );

// echo __PROJECT_ROOT_DIR . '<br>';
// echo __PROJECT_RESOURCE_DIR . '<br>';

// $fsJSON = new \model\io\JSONified( '../private/test1/j.json' );
// // $fsJSON->writeAll( $encoded );
// $x = $fsJSON->readAll();
// if( $x !== false ) {
//     print( implode( ',', $fsJSON->readAll() ) . PHP_EOL );  
// }

// $entity = new Entity( 'id-1014', 'bbb' );
// print( $entity->toString() . PHP_EOL );
// print( $entity->decode( $fsJSON ) . PHP_EOL );
// print( $entity->toString() . PHP_EOL );