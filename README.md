# Cloverlogger
Cloverlogger is a simple file logger for php projects. Cloverlogger is an internal project for Fruitbat Studios/Cloverhitch Technologies/Kludgetastic Implementations.


## Installation
Installation is done via composer:

```bash
composer require gbhorwood/cloverlogger
```

Once installed it is _highly_ recommended to publish the configuration file.

```bash
/vendor/bin/cloverlogger-publish-config
```

## Configuration
The published configuration file lives in the root directory of your project and is called `cloverlogger.conf`

The default config file looks like this.

```
;;;
; Config file for Gbhorwood\Cloverlogger
; https://github.com/gbhorwood/Cloverlogger

FILE=/tmp/cloverlogger
SEPARATOR="::"
```

**`FILE`** Sets the file Cloverlogger writes to.

**`SEPARATOR`** Sets the character(s) used to delimit fields in a log line 

## Usage
A basic usage of cloverlogger looks like:

```php
require_once __DIR__ . '/../vendor/autoload.php';

use Gbhorwood\Cloverlogger\Logger as clover;

clover::info("message", "more message");
```

There are two things to note here:

**method name**: The method name can be anything: `info`, `debug`, `mySpecialLogMessages`. The method name is stored in log line, allowing you to easily find relevant logs. For instance, if you are logging information on about your email library, you could use the method `emailLibLog()`, then find all the logs for that feature by `grep`ing the logfile for that method name.

**arguments**: The method can take an arbitrary number of messages. Each message will be written in the log line, delimted by the `SEPARATOR` character.


## Logged lines
Lines logged by Cloverlogger follow the pattern:

```
<Date as YYYY-mm-dd-HH:ii:ss>::<method name>::<full path to file that wrote the log>::<function that wrote the log, if any>::<line number where log was written>::<message 1>::<any additional messages>
```

Calling Cloverlogger like so:

```php
1 <?php
2
3 function myFunction() {
4     cloverlogger::mySpecialLogMessages("This is a message", "Supplementary message");
5 }
```

will produce a log line that looks like:

```
2025-06-30-15:55:25::mySpecialLogMessages::/path/to/my/script.php::myFunction::4::This is a message::Supplementary message
```

