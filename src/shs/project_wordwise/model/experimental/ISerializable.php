<?php
namespace shs\project_wordwise\model\experimental;
use shs\project_wordwise\model\io\IO;


interface ISerializable {

    public function decode( array | String | IO $source ): bool;
    public function encode( 
        array $encodeAs = [ ISerializable::JSON, ISerializable::ASSOC ] 
    ): array | String | false;

    public const JSON = 0b0000_0001;
    public const YAML = 0b0000_0010;
    public const ASSOC = 0b0000_0100;
}