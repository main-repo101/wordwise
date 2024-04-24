<?php
namespace shs\project_wordwise\model\io;

interface ISerializable {

    public function decode( IO $io ): bool;
    public function encode( int $encodeAs = self::JSON ): String | false;

    public const JSON = 0b0000_0000;
    public const YAML = 0b0000_0001;
}