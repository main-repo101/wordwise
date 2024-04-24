<?php
namespace shs\project_wordwise\model\io;



class FileReader extends Filex implements IReader {

    public function __construct(string $fileName) {
        // echo "<><><><><><> " . $fileName . PHP_EOL;
        // if( !is_file( trim( $fileName ) ) )
        //     throw new \InvalidArgumentException("Invalid file: $fileName" );

        $this->fileName = $fileName;
        $this->handle = false;
        $this->currentPosition = 0;
    }

    private function initHandler(): void {
        $this->handle = fopen($this->fileName, 'r');
        if ($this->handle === false)
            throw new \Exception("Failed to open file: $this->fileName");
    }

    public function __destruct() {
        if ($this->handle !== false) {
            fclose($this->handle);
            if( $this->currentPosition != 0 ) //REM: hmmm... this is not c++ maybe not needed...
                $this->currentPosition = 0;
        }
    }

    public function rewind() {
        $this->currentPosition = 0; //REM: Reset current position to the beginning of the file
    }

    #[\Override]
    public function readLine(): string|false{
        $this->initHandler();
        if ($this->handle === false)
            throw new \Exception("File handler was cleared/closed");

        fseek($this->handle, $this->currentPosition); //REM: Move pointer to current position
        $line = fgets($this->handle); //REM: Read line from current position
        $this->currentPosition = ftell($this->handle); //REM: Update current position
        return $line !== false ? rtrim( $line, "\n\r\t\v" ) : false;
    }
 
    #[\Override]
    public function readAll(): string|false {
        // if( !file_exists( $this->fileName ) )
        //     throw new \RuntimeException( "File does not exist: {$this->fileName}" );
        $contents = file_get_contents( $this->fileName );
        // if( $contents === false )
        //     throw new \RuntimeException( "Failed to read file: {$this->fileName}" );
        return $contents !== false? rtrim( $contents, "\n\r\t\v" ) : false;
    }

    #[\Override]
    public function ready(): bool {
        return !$this->eof();
    }

    public function eof(): bool {
        $this->initHandler();
        if ($this->handle === false) 
            throw new \Exception("File handler was cleared/closed");
        if ( feof( $this->handle ) ) { //REM: automatically rewind if found end-of-line character. Usually it is '\0'
            $this->rewind(); 
            return true;
        }
        return false;
    }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class,
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

    private $handle;
    private int $currentPosition;
}

// $fileReader = new FileReader( '../../private/quest/q.txt' );
// print( $fileReader->toString() . PHP_EOL );


// while( $fileReader->ready() ) {
//     print(  $fileReader->readLine() . "<><><>" . PHP_EOL );
// }
// print(  $fileReader->readLine() . "<><><>" . PHP_EOL );


// print(  $fileReader->readLine() . PHP_EOL );
// print(  $fileReader->readAll() . PHP_EOL );
