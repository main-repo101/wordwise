<?php
namespace shs\project_wordwise\model\io;

use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\Value;

abstract class Filex extends Objectx {

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            $this::class,
            false
        );
    }

    public function getSimpleFileName(): ?String {
        if ( preg_match("/\/([^\/]+)$/i", $this->fileName, $matches ) ) {
            return $matches[1];
        }
        return null;
    }

    public function getPathInfo(): ?array {
        $pathInfo = pathinfo($this->fileName);
        return array(
            'directory' => $pathInfo['dirname'],
            'file_name' => $pathInfo['filename'],
            'base_name' => $pathInfo['basename'],
            'extension' => $pathInfo['extension']
        );
    }

    public function isEmpty(): bool {
        return filesize( $this->fileName ) === 0;
    }
    

    protected String $fileName = Value::NA['VALUE'];
}