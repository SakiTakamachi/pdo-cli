<?php

namespace PDOCli\Console\InputStream;

class DefaultInputStream implements InputStream
{
    public function listen(bool $hasQueryBuffer): string
    {
        return trim(fgets(STDIN));
    }
}
