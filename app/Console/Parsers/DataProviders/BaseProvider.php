<?php


namespace App\Console\Parsers\DataProviders;


use App\Console\Parsers\DataProviders\Contract\ParserInterface;

abstract class BaseProvider implements ParserInterface {
    public static function getParserInstance(string $parserName): ParserInterface {
        $FQN = __NAMESPACE__ . '\\' . $parserName;
        if (!class_exists($FQN)) {
            throw new \InvalidArgumentException("Data provider $parserName not found");
        }
        return new $FQN;
    }
}
