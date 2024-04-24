<?php
namespace shs\project_wordwise\database;
 
use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\Value;
use shs\project_wordwise\model\io\FileReader;
use \PDO;
use shs\project_wordwise\model\io\JSONified;

abstract class DBManagement extends Objectx {

    public function __construct( ?DbCredential $dbCredential = null ) {
        $this->initDB( $dbCredential );
    }

    public function __destruct()
    {
        $this->closeDB();
    }

    //REM: Do we need this type of implementation?
    // public function start(): void { }

    //REM: warning shallow copy '&$dbCredential'...
    private function initDB( ?DBCredential &$dbCredential = null ) {
        $dbCredential = $dbCredential?? new DBCredential();
        try {
            $this->pdo = new PDO( 
                $dbCredential->toString(), 
                $dbCredential->USERNAME, 
                $dbCredential->PASSWORD 
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //REM: *****TODO-HERE;
            $this->dbCredential = $dbCredential;
            //REM: TODO-HERE: update the static counter id via de-serialization/decode, 
            //REM: may make a separated function for it.

            // $data = (new JSONified( __PROJECT_RESOURCE_DIR . 'temp\oh-my-gulay.json'))->readAll();
            // self::$id_counter = $data !== false? $data['id_counter']?? 1 : 1;

            // print( 'CONNECTION SUCCESS...' . PHP_EOL );
        } catch( \PDOException $exception ) {
            //REM: TODO-HERE;
            $this->dbCredential = new DBCredential( 
                Value::NA['VALUE'],
                Value::NA['VALUE'],
                Value::NA['VALUE']
            );
            
            $this->closeDB();
            error_log( "::: ERROR: {$exception->getMessage()}", E_USER_WARNING );
            // exit(); //REM: TODO-HERE; make it smooth error handling...
        }
    }

    public function closeDB( int $transactionToBe = self::ROLL_BACK ): int {
        if( $this->pdo === null)
            return DBClose::ALREADY;

        if( $this->pdo instanceof \PDO ) {
            if( $this->pdo->inTransaction())
                ($transactionToBe === self::COMMIT )? $this->pdo->commit() : $this->pdo->rollback();
            $this->pdo = null;
           // echo ":::CLSOED DB....."  . PHP_EOL; //REM: TODO-HERE...
            return DBClose::SUCCESSED;
        }
        return DBClose::FAILED;
    }

    /**
     * Reset connection...
     */
    public function reset(
        ?int $transactionToBe = null, 
        ?DBCredential $dbCredential = null
    ): bool {
        return $this->resetV2( 
            $transactionToBe, 
            fn():?DBCredential => $dbCredential
        );
    }

    /**
     * Reset connection...
     */
    public function resetV2( 
        ?int $transactionToBe = null, 
        ?\Closure $closure = null
    ): bool {
        if( $this->closeDB( $transactionToBe?? self::ROLL_BACK ) ) {
            //REM: TODO-HERE; Check if it is the right anomymous function or closure. 
            //REM: maybe use some reflection API. (meta-programming)
            if( $closure !== null && $closure() instanceof DBCredential ) {
                $this->dbCredential = $closure();
            }
            $this->initDB( $this->dbCredential ); //REM: TODO-HERE;
            return true;
        }
        return false;
    }

    //REM: Maybe the standard way of doing this hashing is
    //REM: more realiable...
    #[\Override]
    public function hashCode(): String {
        return hash(
            self::HASH_ALGO_SHA_256,
            $this->dbCredential->toString(),
            false
        );
    }

    #[\Override]
    public function equals( ?Objectx $obj ): bool {
        if( parent::equals( $obj ) )
            return true;
        return ( $obj instanceof self ) &&
            $this->dbCredential->toString() === $obj->dbCredential->toString();
    }

    #[\Override]
    public function toString(): String {
        return sprintf( 
            "%s[ host='%s' ]",
            parent::toString(),
            $this->dbCredential->HOST
            /*REM: TODO-HERE; Revealing additional meta-data */
        );
    }

    //REM: TODO-HERE; make it right... refactor it.
    protected function useDB( ?String $dbName ): bool {
        try {
            if( !empty( $dbName = trim( $dbName ) ) && 
                $this->pdo !== null &&
                !$this->pdo->inTransaction() 
            ) {
                $stmt = $this->pdo->prepare( $dbName );
                return $stmt->execute();
            }
        }
        catch( \PDOException | \Error $exception ) {
            //REM: TODO-HERE;
            $this->closeDB();
            
            return false;
            // exit();
        }
        
        return false;
    }

    protected function setSQLStatementFile(FileReader $file, FileReader ...$files): void {
        $this->setSQLStatementFromFile($file);
        foreach ($files as $f)
            $this->setSQLStatementFromFile($f);
    }

    private function setSQLStatementFromFile(FileReader $file): void {
        $fileName = $file->getPathInfo()['file_name'];
        if (preg_match('/.*_(.*)$/i', $fileName, $matches)) {
            $sqlType = strtoupper(trim($matches[1]));
            if( isset( $this->sqlStatement[$sqlType] ) )
                $this->sqlStatement[$sqlType] = $file->readAll();
        }
    }

    public abstract function create( ?Objectx $obj ): bool;
    public abstract function retreive( ?Objectx $obj ): Objectx | false;
    public abstract function update( ?Objectx $obj ): bool;
    public abstract function delete( ?Objectx $obj ): Objectx | false;
    public abstract function getIds(): ?array;

    public function createTable(): bool {
        try {
            if( ( $sql = $this->sqlStatement['TABLE'] ) !== null ) {
                $stmt = $this->pdo->prepare( $sql );
                return $stmt->execute();
            }
        }
        catch ( \PDOException | \ErrorException |  \Exception | \Error $exception ) {
            $this->closeDB();
           // echo ":ERROR createTable: " . $exception->getMessage() . PHP_EOL;
        }
        return false;
    }

    // public function getId(): String {
    //     return sprintf( "%04d", self::$id_counter);
    // }

    public const COMMIT = 0;
    public const ROLL_BACK = 1;

    protected ?array $sqlStatement = [
        "CREATE" => Value::NA['VALUE'],
        "SELECT" => Value::NA['VALUE'],
        "UPDATE" => Value::NA['VALUE'],
        "DELETE" => Value::NA['VALUE'],
        "TABLE" => Value::NA['VALUE'],
        "USE" => Value::NA['VALUE'],
    ];

    private DBCredential $dbCredential;
    protected ?PDO $pdo = null;
    private String $tableName; //REM: TODO-HERE:
    private String $dbName; //REM: TODO-HERE;
    protected String $cacheFileName; //REM: TODO-HERE;
    // protected static int $id_counter = 0;

}

class DBClose {
    public const ALREADY = 1;
    public const SUCCESSED = 2;
    public const FAILED = 4;
}
