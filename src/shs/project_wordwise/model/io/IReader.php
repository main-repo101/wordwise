<?php
namespace shs\project_wordwise\model\io;

interface IReader {
    public function readLine(): array|string|false;
    public function readAll(): array|string|false;
    public function ready(): bool;
}