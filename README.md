# pdo-cli

This is an interactive cli tool (Still under development) that uses PHP PDO.

## Config

~~Please refer to config-sample.json and write the connection information in `config.json` or `config-xxx.json`.~~

Please write all connection information in `db.ini`. Please refer to `db-sample.ini`. You can also specify default values ​​for some settings in config.ini. Please refer to config-sample.ini.

## Install

It requires an autoloader, so run `composer install`.

```
$ composer install
```

## Start

Start the application with the following command: At this time, if a default value for the DB setting name is specified in `config.ini`, that setting will be used. If no default value is specified, the settings listed at the top of `db.ini` will be used.
```
$ php pdo-cli
```

You can also specify the DB setting name as an argument to the command. If specified, the value in `config.ini` is not used.
(The method of specifying DB settings has been changed. ~~`--config-firebird`~~ to `--db=firebird`)
```
$ php pdo-cli --db=firebird
```

## Execute Query

Please write the query. It is executed by typing a semicolon.

Exsample:
```
PDOCli > select 1, 2, 3;
+---+---+---+
| 1 | 2 | 3 |
+---+---+---+
| 1 | 2 | 3 |
+---+---+---+
1 row in set
```

## Close

There are MySQL-like command and PostgreSQL-like command.

```
PDOCli > quit
Bye.

PDOCli > \q
Bye.
```

## Prompt

The prompt defaults to `PDOCli > `, but you can change this to the DB configuration name or driver name. You can set `prompt = db-config-name` or `prompt = driver-name` in `config.ini`.

## Color mode
The following three color modes are available.

- no-color (Mode without color decoration)
- text-color (Mode for changing text color)
- background-color (Mode in which the background color of text changes)

Set these using the `color-mode` key in `config.ini`, or specify them as command arguments like `--color-mode=no-color` at runtime. The standard value is `background-color`.

## Result value type

You can also check what type PDO recognizes each column. To use this feature, write `result-value = pdo-type` in `config.ini` or specify `--result-value=pdo-type` in the command argument.

Note: Having column type data depends on the driver implementation. If the driver does not have column type data, it will all be displayed as Str type.

```
PDOCli > select 1, '2', true;
+---+-----+------+
| 1 | 2   | true |
+---+-----+------+
| 1 | "2" | 1    |
+---+-----+------+
1 row in set
```


## Input stream mode

If you paste a multiline string, the standard input stream will clutter the prompt display (There is no problem with operation). I have prepared a custom input stream to avoid this.

It is automatically applied in environments where the conditions for using a custom input stream are met. Currently it only works in Unix/Linux environments with `pcntl` extension installed.

With custom input stream, you can use the input history by inputting the up and down keys.
