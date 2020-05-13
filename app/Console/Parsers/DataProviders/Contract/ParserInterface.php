<?php


namespace App\Console\Parsers\DataProviders\Contract;


interface ParserInterface {
    public function fetchFeed(): \Generator;
    public function parseNews(): void;
    public function checkActual(): bool;
}
