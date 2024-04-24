<?php
namespace shs\project_wordwise\database;
use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\Participant;
use shs\project_wordwise\model\io\FileReader;
use shs\project_wordwise\model\Value;
use shs\project_wordwise\model\questionnaire\QuestType;

class DBParticipantManagement extends DBManagement {

    public function __construct( ?DBCredential $dBCredential = null ) {
        parent::__construct( $dBCredential );
        try {
            // $this->switchTableByQuestType( QuestType::PRE );
        }
        catch( \Exception | \Error $exception ) {
            $this->closeDB();
            throw $exception;
        }
    }

    // //REM: TODO-HERE; ###
    // public function updateIdCounter(): void {
    //     $counter_id_file = new JSONified( __PROJECT_RESOURCE_DIR . 'temp\oh-my-gulay.json' );
    //     $counter_id_file->writeAll( '{ "id_counter": ' . ++self::$id_counter .' }' );
    // }

    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * 
     * @param String $questType QuestType::PRE or QuestType::POST
     */
    //REM: TODO-HERE; refactor it...
    public function switchTableByQuestType( String $questType = QuestType::PRE ) :void {
        match( $questType) {
            QuestType::POST => (function(){
                $this->setSQLStatementFile(
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/word_wise_db_use.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/post-test/participant_post_test_create.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/post-test/participant_post_test_select.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/post-test/participant_post_test_update.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/post-test/participant_post_test_delete.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/post-test/participant_post_test_table.sql' )
                );
            })(),
            default => (function(){
                $this->setSQLStatementFile(
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/word_wise_db_use.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/pre-test/participant_pre_test_create.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/pre-test/participant_pre_test_select.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/pre-test/participant_pre_test_update.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/pre-test/participant_pre_test_delete.sql' ),
                    new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/participant/pre-test/participant_pre_test_table.sql' )
                );
            })()
        };
        $this->useDB( $this->sqlStatement['USE'] );
        $this->createTable();
    }
    public function create( ?Objectx $obj ): bool {
        if( !( $obj instanceof Participant ) )
            return false;
        try {

            if( $this->retreive( $obj ) !== false )
                return false;
            $this->pdo->beginTransaction();

            //REM: TODO-HERE: oh my... fix it later...
            // $x = [ QuestType::PRE, QuestType::POST ];
            // for( $i = 0; $i < count( $x ); ++$i ) { /*REM: BEGIN; 0x01; For-loop */
            // $this->switchTableByQuestType( $x[$i] );

            $stmt = $this->pdo->prepare( $this->sqlStatement['CREATE'] );

            //REM: :name, :age, :rate, :points, :total_item_points, :number_of_items
            $participantId = $obj->getId();
            $userName = $obj->getUsername();//REM: ###
            $password = $obj->getPassword();//REM: ###
            $firstName =  $obj->getFirstName();
            $middleName =  $obj->getMiddleName();
            $lastName =  $obj->getLastName();
            $age = $obj->getAge();
            $scoreRate = $obj->score->getRate();
            $scorePoints = $obj->score->getPoints();
            $scoreTotalItemPoints = $obj->score->getTotalItemsPoints();
            $scoreTotalItems = $obj->score->getTotalItems();
            $scoreEachItemPoints = $obj->score->getEachItemPoints(); //REM: ###

            // $stmt->bindParam(':id', self::$id_counter );
            $stmt->bindParam(':participant_id', $participantId );
            $stmt->bindParam(':user_name', $userName );//REM: ###
            $stmt->bindParam(':password', $password ); //REM: ###
            $stmt->bindParam(':first_name', $firstName );
            $stmt->bindParam(':middle_name', $middleName );
            $stmt->bindParam(':last_name', $lastName );
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':rate', $scoreRate );
            $stmt->bindParam(':points', $scorePoints );
            $stmt->bindParam(':total_item_points', $scoreTotalItemPoints );
            $stmt->bindParam(':total_items', $scoreTotalItems );
            $stmt->bindParam(':each_item_points', $scoreEachItemPoints ); //REM: ###

            $stmt->execute();
            //REM: self::$id_counter = $this->pdo->lastInsertId();
            //REM: echo '::...' . self::$id_counter . PHP_EOL;
                
            // }//REM: END; 0x01; For-loop
            if( $this->pdo->commit() ) {
                $obj->updateIdCounter();
                $obj->updateSession();
                //REM:TODO-HERE; ##### ASAP
                return true;
            }
        } catch( \PDOException | \Error $exception ) {
            if( $this->pdo && $this->pdo->inTransaction() )
                $this->pdo->rollBack(); //REM: ??? maybe this is already ommitted?
            
            // echo ":::ERROR DB CREATE: " . $exception->getMessage() . PHP_EOL;
        }

        return false;
    }
    public function retreive(?Objectx $obj): Participant | false {
        if (!($obj instanceof Participant))
            return false;
    
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($this->sqlStatement['SELECT']);
    
            $userName = $obj->getUsername();
            // echo "username.........:" . $userName . PHP_EOL;
            $stmt->bindParam(':user_name', $userName); //REM: TODO_HERE; ### 
            $stmt->execute();
    
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);

    
            if ($data) {
                $obj = new Participant( Value::NA['VALUE'] );
                //REM: TODO-HERE; also retreive the participant-id from the data base;
                $obj->setId( $data['participant_id'] );
                //REM: User account exists, populate the Participant object and return
                $obj->setUsername($data['user_name']);//REM: ###
                $obj->setFirstName($data['first_name']);
                $obj->setMiddleName($data['middle_name']);
                $obj->setLastName($data['last_name']);
                $obj->setAge($data['age']?? 0);
                $obj->score->setPoints( $data['points']?? 0 );
                $obj->score->setTotalItems( $data['total_items']?? 0 );
                $obj->score->setEachItemPoints( $data['each_item_points']?? 1 );
                // $obj->updateSession();
                $this->pdo->commit();
                return $obj;
            } else 
                throw new \PDOException( "Participant account does not exist, rollback transaction and return false" );
        } catch (\PDOException | \Error $exception) {
            if ($this->pdo && $this->pdo->inTransaction())
                $this->pdo->rollBack(); //REM: Roll back the transaction if it's in progress
            // echo ":::ERROR DB retreive: " . $exception->getMessage() . PHP_EOL;
        }
        return false;
    }
    
    public function update( ?Objectx $obj ): bool {
        if( !( $obj instanceof Participant ) )
            return false;
        try {
            // echo "<><><><><> " . $obj->getLastName() . PHP_EOL;
            // echo $obj->toString() . PHP_EOL;
            if( $this->retreive( $obj ) === false )
                return false;

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare( $this->sqlStatement['UPDATE'] );
            $userName = $obj->getUsername();
            $password = $obj->getPassword();
            $firstName =  $obj->getFirstName();
            $middleName =  $obj->getMiddleName();
            $lastName =  $obj->getLastName();
            $age = $obj->getAge();
            $scoreRate = $obj->score->getRate();
            $scorePoints = $obj->score->getPoints();
            $scoreTotalItemPoints = $obj->score->getTotalItemsPoints();
            $scoreTotalItems = $obj->score->getTotalItems();
            $scoreEachItemPoints = $obj->score->getEachItemPoints();


            if( $stmt->execute( [
                'user_name' => $userName,
                'password' => $password,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'age' => $age,
                'rate' => $scoreRate,
                'points' => $scorePoints,
                'total_item_points' => $scoreTotalItemPoints,
                'total_items' => $scoreTotalItems,
                'each_item_points' => $scoreEachItemPoints,
            ]) ) {
                $obj->updateSession();
                return $this->pdo->commit();
            }

            throw new \PDOException( 'Cannot Update...' ); //REM: TODO-HERE: handle it... Add more info about the said exception
        }
        catch ( \PDOException | \ValueError | \ErrorException | \Error $exception ) {
            if ($this->pdo && $this->pdo->inTransaction())
                $this->pdo->rollBack(); //REM: Roll back the transaction if it's in progress
            // echo ":::ERROR DB UPDATE: " . $exception->getMessage() . PHP_EOL;
        }
        return false;
    }

 
    public function delete( ?Objectx $obj ): Participant | false {
        if (!($obj instanceof Participant))
            return false;
    
        try {
            $previousData = $this->retreive( $obj );

            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($this->sqlStatement['DELETE']);

            if ( $previousData &&
                $stmt->execute( [ 'participant_id' => $previousData->getId() ])
            ) {
                $this->pdo->commit();
                return $previousData;
            } else 
                throw new \PDOException( "Participant account deletion failed, rollback transaction and return false" );
        } catch (\PDOException | \Error $exception) {
            if ($this->pdo && $this->pdo->inTransaction())
                $this->pdo->rollBack(); //REM: Roll back the transaction if it's in progress
            // echo ":::ERROR DB DELETE: " . $exception->getMessage() . PHP_EOL;
        }
        return false;
    }

    //REM: TODO-HERE; We need to sync it with our '$sqlStatement' 
    public function getIds( String $tableName = Self::TABLE_PARTICIPANT_PRE_TEST ): ?array {
        try {
            
            // $this->switchTableByQuestType( $questType );

            // Prepare the SQL statement with named placeholders
            $stmt = $this->pdo->prepare("SELECT :participant_id FROM :table_name");
            $idColumn = 'participant_id';
            // Bind parameters
            $stmt->bindParam(':participant_id', $idColumn);
            $stmt->bindParam(':table_name', $table_name);
            
            // Execute the statement
            $stmt->execute();
            
            // Fetch all rows as an associative array
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Extract IDs from the result rows
            $ids = array_column($rows, $idColumn);
            
            return $ids;
        } catch (\PDOException $e) {
            // Handle any database errors
            // You can log the error or return an empty array, depending on your needs
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
    
    // #[\Override]
    // public function getId(): String {
    //     return sprintf("%s-%04d", Participant::PARTICIPANT_PREFIX_ID, self::$id_counter );
    // }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            $this::class,
            false
        );
    }

    //REM: TODO-HERE, we need to sync it with our '$sqlStatement' from the 'DBManagement'
    public const TABLE_PARTICIPANT_PRE_TEST = 'participant_pre_test';
    public const TABLE_PARTICIPANT_POST_TEST = 'participant_post_test';
}