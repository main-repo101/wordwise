<?php
namespace shs\project_wordwise\model\io;

interface IWriter {
    public function writeLine( String $content, int $option = 0 ): int | false;
    public function writeAll( String $content, int $option = 0 ): int | false;
}