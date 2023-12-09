# pdo-cli

This is an interactive cli tool (Still under development) that uses PHP PDO.

## Config

Please refer to config-sample.json and write the connection information in `config.json` or `config-xxx.json`.

## Start

(default) When using settings from `config.json`:
```
$ php pdo-cli
```

When using `config-firebird.json`:
```
$ php pdo-cli --config-firebird
```

## Execute sql

Please write the sql. It is executed by typing a semicolon.

Exsample:
```
pdo-cli > select 1,2,3;
+---+---+---+
| 1 | 2 | 3 |
+---+---+---+
| 1 | 2 | 3 |
+---+---+---+
```

## Close

There are MySQL-like command and PostgreSQL-like command.

```
pdo-cli > quit
Bye.

pdo-cli > \q
Bye.
```
