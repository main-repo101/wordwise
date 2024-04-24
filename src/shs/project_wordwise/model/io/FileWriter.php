<?php
namespace shs\project_wordwise\model\io;

use shs\project_wordwise\model\Objectx;

class FileWriter extends Filex implements IWriter {

    public function __construct( string $fileName, ?int $option = null ) {
        // if( !is_file( trim( $fileName ) ) )
        //     throw new \InvalidArgumentException("Invalid file: $fileName" );
        $this->fileName = $fileName;
        $this->option = $option?? 0;
    }

    public function __destruct() { 
        //REM: ignore...
    }

    #[\Override]
    public function writeLine( String $content, int $option = 0 ): int | false {
        return $this->writeAll( $content . PHP_EOL, $option );
    }
 
    #[\Override]
    public function writeAll( String $content, int $option = 0 ): int | false{
        if( !realPath( $this->fileName ) )  {
            $dirPath = dirname($this->fileName);
            if (!file_exists($dirPath)) 
                mkdir($dirPath, 0777, true); //REM: Recursive directory creation
            if( !touch( $this->fileName ) )
                throw new \Exception("Trying to create the said file as it is not existed but something went wrong: '$this->fileName'" );
        }
            
        $result = file_put_contents(
            $this->fileName, 
            $content, 
            ($option !== 0)? $option : $this->option
        );
        if ($result === false)
            throw new \Exception("Failed to write to file: '$this->fileName'" );
        return $result;
    }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class . $this->fileName,
            false
        );
    }

    #[\Override]
    public function toString(): String {
        return sprintf(
            "%s[ fileName='%s' ]",
            parent::toString(),
            $this->fileName
        );
    }

    private String $option;
}

// $fileWriter = new FileWriter( '../../private/quest/q.txt' );
// print( $fileWriter->toString() . PHP_EOL );


// print(  $fileWriter->writeLine( 'ok' ) . "<><><>" . PHP_EOL );

