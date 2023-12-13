<?php

namespace PDOCli\Console\InputStream;

interface InputStream
{
    public function listen(bool $hasQueryBuffer): string;
}
