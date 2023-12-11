<?php

define('PROMPT_LEN', 10);

global $arrowPrefix;

function getPrompt(bool $needNextLine = false): string
{
    return $needNextLine ? str_repeat(' ', 8).'> ' : 'pdo-cli > ';
}

function consoleOutput(string $prompt, array $buf, int $cursor, $toNextRow)
{
    echo "\r".$prompt.$buf[0].$buf[1];

    if (! $toNextRow) {
        echo "\r".str_repeat(RIGHT_ARROW, $cursor + PROMPT_LEN);
    }
}

function refreshLine(int $len)
{
    echo "\r".str_repeat(' ', $len + PROMPT_LEN);
}

function main(): void
{
    $sql = '';
    echo getPrompt();
    while (true) {
        $input = '';
        $buf = ['', ''];
        $cursor = 0;
        $prompt = getPrompt($sql);

        while ($input !== PHP_EOL) {
            $input = fgetc(STDIN);

            if ($input === ARROW_PREFIX) {
                $input .= fgets(STDIN, 3);

                if (in_array($input, [UP_ARROW, DOWN_ARROW], true)) {
                    continue;
                }

                if (in_array($input, [RIGHT_ARROW, LEFT_ARROW], true)) {
                    if (($input === LEFT_ARROW && $cursor - 1 < 0) ||
                        ($input === RIGHT_ARROW && $cursor >= strlen($buf[0].$buf[1]))
                    ) {
                        continue;
                    }

                    $cursor += $input === RIGHT_ARROW ? 1 : -1;
                    $buf2 = $buf[0].$buf[1];
                    $buf[0] = substr($buf2, 0, $cursor);
                    $buf[1] = substr($buf2, $cursor);
                    echo $input;
                    continue;
                }
            }

            if ($input === BACKSPACE) {
                if (strlen($buf[0]) === 0) {
                    continue;
                }
                $buf0len = strlen($buf[0]);
                $buflen = $buf0len + strlen($buf[1]);
                refreshLine($buflen);
                $buf[0] = substr($buf[0], 0, $buf0len - 1);
                $cursor--;
            } elseif ($input === PHP_EOL) {
                $buf[1] .= $input;
                $cursor++;
            } else {
                $buf[0] .= $input;
                $cursor++;
            }

            consoleOutput($prompt, $buf, $cursor, $input === PHP_EOL);
        }

        $sql .= ($sql ? ' ' : '').trim($buf[0].$buf[1]);
        if ($sql === 'quit' || $sql === '\\q') {
            break;
        }
        if (strpos($sql, ';') !== false) {
            $sqls = explode(';', $sql);
            $toExec = $sqls[0];
            $sql = $sqls[1] ?? '';

            exec_sql($toExec);
        }

        echo getPrompt($sql);
    }
    
   exit();
}
