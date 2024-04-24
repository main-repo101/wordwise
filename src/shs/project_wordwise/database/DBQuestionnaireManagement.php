<?php
namespace shs\project_wordwise\database;
use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\io\JSONified;
use shs\project_wordwise\model\io\FileReader;
use shs\project_wordwise\model\Value;
use shs\project_wordwise\model\questionnaire\Level;

class DBQuestionnaireManagement extends DBManagement {

    public function __construct( ?DBCredential $dBCredential = null ) {
        parent::__construct( $dBCredential );
        try {
            $this->setSQLStatementFile(
                new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/word_wise_db_use.sql' ),
                new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/quest-and-answer/quest_and_answer_create.sql' ),
                new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/quest-and-answer/quest_and_answer_select.sql' ),
                new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/quest-and-answer/quest_and_answer_update.sql' ),
                new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/quest-and-answer/quest_and_answer_delete.sql' ),
                new FileReader( __PROJECT_PRIVATE_RESOURCE_DB_DIR . 'sql/quest-and-answer/quest_and_answer_table.sql' )
            );
            $this->useDB( $this->sqlStatement['USE'] );
            $this->createTable();
        }
        catch( \Exception | \Error $exception ) {
            $this->closeDB();
            // echo ":::ERROR DB INIT: " . $exception->getMessage() . PHP_EOL;
            throw $exception;
        }
    }
    public function create( ?Objectx $obj ): bool {
        if( !( $obj instanceof QuestAndAnswerSingleMap ) )
            return false;
        // if( $obj->isNotSet() )
        //     return false;
            
        
        // if( !($quest instanceof Quest ) || !( $answer instanceof Answer ) )
        //     return false;
        try {
            if( $this->retreive( $obj ) !== false )
                return false;
            
            $quest = $obj->QUEST;
            // $answer = $obj->ANSWER;

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare( $this->sqlStatement['CREATE'] );

            //REM: qa_id VARCHAR(255) PRIMARY KEY,
            //REM: quest_type VARCHAR(255),
            //REM: quest_level VARCHAR(255),
            //REM: quest_filename VARCHAR(255),
            //REM: answer_filename VARCHAR(255)
      
            $qaId = $quest->ID;
            $qaType = $quest->QUEST_TYPE;
            $qaLevel = $quest->getLevel();
            // $qaQuestion = $quest->QUESTION;
            // // $qaChoices= implode( ", ", $quest->CHOICES );
            // $qaChoices = $quest->CHOICES;
            // $qaHint = $quest->HINT;
            // // $qaAnswers = implode( ", ", $answer->ANSWERS );
            // $qaAnswers = $answer->ANSWERS;

            
            //REM: This is for checking if we have the same content QnA.
            //REM: Yes, indeed we might suffer from a hash collision, but
            //REM: this is only for a quick fix. 
            $qaContentHash = $obj->hashCOde();

            // //REM: TODO-HERE; refactor it...
            // $questFilename = 'private' . DIRECTORY_SEPARATOR . 
            //     'res' . DIRECTORY_SEPARATOR .
            //     'quest' . DIRECTORY_SEPARATOR . 
            //     strtolower( $qaType ) . '-test' . DIRECTORY_SEPARATOR . 
            //     $qaId . '.json';
            // $answerFilename = 'private' . DIRECTORY_SEPARATOR . 
            //     'res' . DIRECTORY_SEPARATOR . 
            //     'answer' . DIRECTORY_SEPARATOR . 
            //     strtolower( $qaType ) . '-test' . DIRECTORY_SEPARATOR . 
            //     $qaId . '.json';
                
            // $questIO = new JSONified( __PROJECT_ROOT_DIR . $questFilename );
            // $answerIO = new JSONified( __PROJECT_ROOT_DIR . $answerFilename );
            
            //REM: TODO-HERE; refactor it...
            $questFilename = self::QUESTION_FILE_PATH .
                strtolower( $qaType ) . '-test' . DIRECTORY_SEPARATOR .
                strtolower( $qaId ) . '.json';

            $answerFilename = self::ANSWER_FILE_PATH . 
                strtolower( $qaType ) . '-test' . DIRECTORY_SEPARATOR .
                strtolower( $qaId ) . '.json';

            $stmt->bindParam(':qa_id', $qaId );
            $stmt->bindParam(':qa_content_hash', $qaContentHash );
            $stmt->bindParam(':quest_type', $qaType );
            $stmt->bindParam(':quest_level', $qaLevel );
            $stmt->bindParam(':quest_filename', $questFilename );
            $stmt->bindParam(':answer_filename', $answerFilename );

            $stmt->execute();
            
            if( $this->pdo->commit() ) {
                //REM: TODO-HERE; yap I know, it will be fixed later on...
                // $questIO->writeAll( (String)`{
                //     "qa_id" : "$qaId",
                //     "quest_type" : "$qaType",
                //     "quest_level" : "$qaLevel",
                //     "question" : [ $qaQuestion ],
                //     "hint" : "$qaHint",
                //     "choices" : "$qaChoices",

                //     "quest_filename" : "$questFilename"
                // }`);

                // //REM: TODO-HERE; yap I know, it will be fixed later on...
                // $questIO->writeAll (
                //     json_encode(
                //         [
                //             "qa_id" => $qaId,
                //             "qa_content_hash" => $qaContentHash,
                //             "quest_type" => $qaType,
                //             "quest_level" => $qaLevel,
                //             "question" => $qaQuestion,
                //             "hint" => $qaHint,
                //             "choices" => $qaChoices,
                            
                //             "quest_filename" => $questFilename
                //         ],
                //         JSON_PRETTY_PRINT
                //     )
                // );

                // //REM: TODO-HERE; yap I know, it will be fixed later on...
                // // $answerIO->writeAll( (String)`{
                // //     "qa_id" : "$qaId",
                // //     "answers" " : [ $qaAnswers ],
                // //     "answer_filename" : "$answerFilename"
                // // }`);
                // $answerIO->writeAll(
                //     json_encode(
                //         [
                //             "qa_id" => $qaId,
                //             "qa_content_hash" => $qaContentHash,
                //             "answers" => $qaAnswers,
                //             "answer_filename" => $answerFilename
                //         ],
                //         JSON_PRETTY_PRINT
                //     )
                // );

                self::writeIt( $questFilename, $answerFilename, $obj );
                $obj->updateIdCounter();
                return true;
            }
        } catch( \PDOException | \Error $exception ) {
            if( $this->pdo && $this->pdo->inTransaction() )
                $this->pdo->rollBack(); //REM: ??? maybe this is already ommitted?
            
            error_log("::: PHP_DEBUG: Database error 'CREATE' : " . $this->toString() . '; ' . $exception->getMessage());

            // throw $exception;
        }

        return false;
    }

    private static function writeIt( 
        String $questFilename,
        String $answerFilename,
        QuestAndAnswerSingleMap &$qAASMap 
    ): void {
        
        $quest = $qAASMap->QUEST;
        $answer = $qAASMap->ANSWER;

        $qaId = $quest->ID;
        $qaType = $quest->QUEST_TYPE;
        $qaLevel = $quest->getLevel();
        $qaQuestion = $quest->QUESTION;
        // $qaChoices= implode( ", ", $quest->CHOICES );
        $qaChoices = $quest->CHOICES;
        $qaHint = $quest->HINT;
        // $qaAnswers = implode( ", ", $answer->ANSWERS );
        $qaAnswers = $answer->ANSWERS;

        $qaContentHash = $qAASMap->hashCOde();

        $questIO = new JSONified( __PROJECT_ROOT_DIR . $questFilename );
        $answerIO = new JSONified( __PROJECT_ROOT_DIR . $answerFilename );

        $questIO->writeAll (
            json_encode(
                [
                    "qa_id" => $qaId,
                    "qa_content_hash" => $qaContentHash,
                    "quest_type" => $qaType,
                    "quest_level" => $qaLevel,
                    "question" => $qaQuestion,
                    "hint" => $qaHint,
                    "choices" => $qaChoices,
                    
                    "quest_filename" => $questFilename
                ],
                JSON_PRETTY_PRINT
            )
        );

        //REM: TODO-HERE; yap I know, it will be fixed later on...
        // $answerIO->writeAll( (String)`{
        //     "qa_id" : "$qaId",
        //     "answers" " : [ $qaAnswers ],
        //     "answer_filename" : "$answerFilename"
        // }`);
        $answerIO->writeAll(
            json_encode(
                [
                    "qa_id" => $qaId,
                    "qa_content_hash" => $qaContentHash,
                    "answers" => $qaAnswers,
                    "answer_filename" => $answerFilename
                ],
                JSON_PRETTY_PRINT
            )
        );

    }
    public function retreive( ?Objectx $obj ): QuestAndAnswerSingleMap | false {
        if( !( $obj instanceof QuestAndAnswerSingleMap ) )
            return false;
        // if( $obj->isNotSet() )
        //     return false;
                
        // $quest = $obj->getMap()['QUEST'];
        // $answer = $obj->getMap()['ANSWER'];
        $quest = $obj->QUEST;
        // $answer = $obj->ANSWER;
        
        // if( !($quest instanceof Quest ) || !( $answer instanceof Answer ) )
        //     return false;

        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($this->sqlStatement['SELECT']);

            $qaId = $quest->ID;

            // echo "1111qa_id.........:" . $qaId . ':' . PHP_EOL;
            $stmt->bindParam(':qa_id', $qaId); //REM: TODO_HERE; ### 
            $stmt->execute();
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($data && $this->pdo->commit() ) {
               
                $questData = (new JSONiFied( __PROJECT_ROOT_DIR . $data['quest_filename'] ))->readAll();
                $answerData = (new JSONiFied( __PROJECT_ROOT_DIR . $data['answer_filename'] ))->readAll();
                
                $qASingleMap = new QuestAndAnswerSingleMap();
                $qASingleMap->absorb( 
                    $questData['qa_id']?? Value::NA['VALUE'],
                    $questData['question']?? Value::NA['VALUE'],
                    $questData['choices']?? [],
                    $questData['quest_type'],
                    Level::getObjLevel( $questData['quest_level'] ),
                    $questData['hint'],
                    $answerData['answers']?? []
                );
                return $qASingleMap;
            } else 
                throw new \PDOException( "Quest item does not exist: '$qaId', rollback transaction and return false" );
        
        }
        catch( \PDOException | \Error $exception ) {
            // $this->closeDB();

            if( $this->pdo && $this->pdo->inTransaction() )
                $this->pdo->rollBack();
            
            error_log("::: PHP_DEBUG: Database error 'RETRIEVE' : " . $this->toString() . '; ' . $exception->getMessage());

        }

        return false;
    }
    public function update( ?Objectx $obj ): bool {
        if( !( $obj instanceof QuestAndAnswerSingleMap ) )
            return false;
        try {
            
            if( $this->retreive( $obj ) === false )
               return false;

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare( $this->sqlStatement['UPDATE'] );

            $quest = $obj->QUEST;
            // $answer = $obj->ANSWER;
            
            $qaId = $quest->ID;
            $qaType = $quest->QUEST_TYPE;
            $qaContentHash = $obj->hashCode();
            $qaLevel = $quest->getLevel();

            
            // //REM: TODO-HERE; refactor it... 
            // //REM: TODO-HERE; forgot to add file dir for both questions and answers
            // $questFilename = self::QUESTION_FILE_PATH .
            //     strtolower( $qaType ) . '-test' . DIRECTORY_SEPARATOR .
            //     strtolower( $qaId ) . '.json';

            // $answerFilename = self::ANSWER_FILE_PATH . 
            //     strtolower( $qaType ) . '-test' . DIRECTORY_SEPARATOR .
            //     strtolower( $qaId ) . '.json';

            // $qaQuestion = $quest->QUESTION;
            // $qaHint = $quest->HINT;
            // $qaChoices = $quest->CHOICES;
            // $qaAnswers = $answer->ANSWERS;
            
            //REM: quest_type = :quest_type,
            //REM: qa_content_hash = :qa_content_hash,
            //REM: quest_level = :quest_level,
            //REM: quest_filename = :quest_filename,
            //REM: answer_filename = :answer_filename

            // echo "qa_id.........:" . $id . PHP_EOL;
            $stmt->bindParam(':qa_id', $qaId); //REM: TODO_HERE; ### 
            $stmt->bindParam(':qa_content_hash', $qaContentHash );
            $stmt->bindParam(':quest_type', $qaType );
            $stmt->bindParam(':quest_level', $qaLevel );
            // $stmt->bindParam(':quest_filename', $questFilename ); //REM: TODO-HERE; forgot to add file dir for both questions and answers
            // $stmt->bindParam(':answer_filename', $answerFilename ); //REM: TODO-HERE; forgot to add file dir for both questions and answers

            $stmt->execute();

            $this->pdo->commit();

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($this->sqlStatement['SELECT']);
            $stmt->bindParam(':qa_id', $qaId);
            
            $stmt->execute();

            $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($data && $this->pdo->commit() ) {

                // $qASingleMap = new QuestAndAnswerSingleMap();
                // $qASingleMap->absorb( 
                //     $qaId,
                //     $qaQuestion,
                //     $qaChoices,
                //     $qaType,
                //     $quest->LEVEL,
                //     $qaHint,
                //     $qaAnswers
                // );
               
                self::writeIt( 
                    $data['quest_filename'], 
                    $data['answer_filename'],
                    $obj
                );
                return true;
            } else 
                throw new \PDOException( "Quest item failed to update. Problem Quest does not exist: '$qaId' or the said Question and Answers already exist, rollback transaction and return false" );
        }
        catch ( \PDOException | \ValueError | \ErrorException | \Error $exception ) {
            if ($this->pdo && $this->pdo->inTransaction())
                $this->pdo->rollBack(); //REM: Roll back the transaction if it's in progress
            error_log("::: PHP_DEBUG: Database error 'UPDATE' : " . $this->toString() . '; ' . $exception->getMessage());

        }
        return false;
    }
    public function delete( ?Objectx $obj ): QuestAndAnswerSingleMap | false {
        if (!($obj instanceof QuestAndAnswerSingleMap))
            return false;
    
        try {
            
            $previousData = $this->retreive( $obj );
            

            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($this->sqlStatement['DELETE']);


            if ( $previousData &&
                $stmt->execute( [ 'qa_id' => $previousData->QUEST->ID ])
            ) {
                $this->pdo->commit();
                return $previousData;
            } else 
                throw new \PDOException( "Quest Item deletion failed: " . $$previousData->QUEST->ID . ", rollback transaction and return false" );
        } catch (\PDOException | \Error $exception) {
            if ($this->pdo && $this->pdo->inTransaction())
                $this->pdo->rollBack(); //REM: Roll back the transaction if it's in progress
            error_log("::: PHP_DEBUG: Database error 'DELETE' : " . $this->toString() . '; ' . $exception->getMessage());
        }
        return false;
    }

    public function getIds(): ?array {
        try {
            
            $idColumn = 'qa_id';
            $tableName = 'quest_and_answer';
            $stmt = $this->pdo->prepare("SELECT $idColumn FROM $tableName");
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $ids = array_column( $rows, $idColumn );

            // print( implode(' | ', $ids ) );
            // print( 'getIDs.....' . PHP_EOL );
            
            return $ids;
        } catch (\PDOException | \ErrorException | \Error $exception) {
            // Handle any database errors
            // You can log the error or return an empty array, depending on your needs
            error_log(
                sprintf(
                    "::: PHP_DEBUG: Database error 'getIds()' : %s; %s", 
                    $this->toString(), $exception->getMessage()
                ),
                E_USER_ERROR
            );
            return [];
        }
    }

    private const QUESTION_FILE_PATH = 'private' . DIRECTORY_SEPARATOR . 
    'res' . DIRECTORY_SEPARATOR . 
    'quest' . DIRECTORY_SEPARATOR;

    private const ANSWER_FILE_PATH = 'private' . DIRECTORY_SEPARATOR . 
    'res' . DIRECTORY_SEPARATOR . 
    'answer' . DIRECTORY_SEPARATOR;
}