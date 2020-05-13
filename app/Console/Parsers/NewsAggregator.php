<?php


namespace App\Console\Parsers;


use App\Console\Parsers\DataProviders\Contract\ParserInterface;

class NewsAggregator {
    public function __invoke(ParserInterface $parser) {
        $parser->parseNews();
    }
}
