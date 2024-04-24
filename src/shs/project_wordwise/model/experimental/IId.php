<?php
namespace shs\project_wordwise\model\experimental;

interface IId {
    
    public function setId( String $id, String $prefix = "" ): void;

}