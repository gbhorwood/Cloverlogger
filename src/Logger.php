<?php

declare(strict_types=1);

namespace Gbhorwood\Cloverlogger;

/**
 * MIT License
 *
 * Copyright (c) 2025 grant horwood
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

class Logger
{

    /**
     * CallStatic to handle user defined methods. 
     *
     * @param  string $method
     * @param  array $args
     * @return void
     */
    public static function __callStatic($method, $args): void
    {
        self::_doLog($method, $args);
    }

    /**
     * Generate log line and write to file.
     *
     * @param  string $method
     * @param  array $args
     * @return void
     * @throws Exception
     */
    private static function _doLog($method, $args)
    {
        // extract configuration data. fall back to defaults.
        $config = self::_config();
        $separator = $config['SEPARATOR'] ?? '::';
        $file = $config['FILE'] ?? '/tmp/cloverlog';

        // current date
        $now = date("Y-m-d-H:i:s");

        // get info on code that called us: file, function and line.
        $caller = self::_callerData();

        // build log line
        $line = $now.$separator.$method.$separator.$caller->file.$separator.$caller->function.$separator.$caller->line.$separator.join($separator, $args).PHP_EOL;

        // write to disk
        self::_write($line, $file);
    }

    /**
     * Write line $line to file at $file
     *
     * @param  string $line
     * @param  string $file
     * @return void
     * @throws Exception
     */
    private static function _write(string $line, string $file)
    {
        $fp = @fopen($file, 'a');
        if(!$fp) {
            throw new \Exception("Cloverlogger could not write to file '$file'");
        }
        fwrite($fp, $line);
        fclose($fp);
    }

    /**
     * Parse config file from root project and return as array
     *
     * @return array<string,string>
     */
    private static function _config(): array
    {
        $config = @parse_ini_file(dirname(__DIR__, 2)."cloverlogger.conf");
        return $config ? $config : [];
    }

    /**
     * Extract file, function and line that called __callStatic() function
     * from Exception stack trace. Return as object.
     *
     * @return object
     */
    private static function _callerData(): object
    {
        $e = new \Exception();
        $t = $e->getTrace();
        $file = $t[2]['file'];
        // if not called from a function, return '-'
        $function = $t[3]['function'] ?? "-";
        $line = $t[2]['line'];

        return (object)[
            'file' => $file,
            'function' => $function,
            'line' => $line,
        ];
    }
}
