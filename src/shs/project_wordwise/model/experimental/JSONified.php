<?php
namespace shs\project_wordwise\model\experimental;
use shs\project_wordwise\model\io\IO;
use shs\project_wordwise\model\io\FileWriter;
use shs\project_wordwise\model\io\FileReader;

//REM: TOOD-HERE; more factoring and optimization... And is this a good design/structure?...
//REM: For now this will suffice...
class JSONified extends IO {

    public function __construct( String $fileName ) {
        $this->fileReader = new FileReader( $fileName );
        $this->fileWriter= new FileWriter( $fileName );
    }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class . $this->fileWriter->hashCode() . $this->fileReader->hashCode(),
            false
        );
    }

    #[\Override]
    public function readLine(): array|String|false {
        return json_decode( $this->fileReader->readAll(), true )?? false;
    }

    #[\Override]
    public function readAll(): array|false {
        return json_decode( $this->fileReader->readAll(), true )?? false;
    }

    #[\Override]
    public function ready(): bool {
        return $this->fileReader->ready();
    }

    #[\Override]
    public function writeLine( String $content, int $option = 0 ): int | false {
        return $this->fileWriter->writeLine( $content, $option );
    }

    
    #[\Override]
    public function writeAll( String $content, int $option = 0 ): int | false {
        return $this->fileWriter->writeAll( 
            json_encode( $content, JSON_PRETTY_PRINT ) .
            ( ( $this->fileWriter->isEmpty() ) ? '' :  ( ( $option !== 1 )? '' : ',' ) ),
            $option 
        );
    }

    public function rewind() {
        $this->fileReader->rewind();
    }

    public FileReader $fileReader;
    public FileWriter $fileWriter;
}

// $jsonified = new JSONified( 'adsf' );
// print( $jsonified->toString() );