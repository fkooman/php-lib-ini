<?php

/**
 * Copyright 2015 François Kooman <fkooman@tuxed.net>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace fkooman\Ini;

use RuntimeException;
use InvalidArgumentException;

class IniReader
{
    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function fromFile($configFile)
    {
        $fileContent = @file_get_contents($configFile);
        if (false === $fileContent) {
            throw new RuntimeException('unable to read configuration file');
        }
        $configData = @parse_ini_string($fileContent, true);
        if (false === $configData) {
            throw new RuntimeException('unable to parse configuration file');
        }

        return new static($configData);
    }

    public static function isRequired(array $argv)
    {
        foreach ($argv as $arg) {
            if (is_string($arg)) {
                continue;
            } elseif (is_bool($arg)) {
                return $arg;
            } else {
                throw new InvalidArgumentException('invalid argument type');
            }
        }

        return true;
    }

    public static function defaultValue(array $argv)
    {
        $argc = count($argv);
        for ($i = 1; $i < $argc; ++$i) {
            if (false === $argv[$i]) {
                // return next as default value
                if (array_key_exists($i + 1, $argv)) {
                    return $argv[$i + 1];
                }

                return;
            }
        }
    }

    public static function configValues(array $argv)
    {
        $configValues = array();
        foreach ($argv as $arg) {
            if (!is_string($arg)) {
                break;
            }
            $configValues[] = $arg;
        }

        return $configValues;
    }

    private static function configExists(array $configPointer, array $argv)
    {
        foreach ($argv as $arg) {
            if (!is_array($configPointer)) {
                return false;
            }
            if (!array_key_exists($arg, $configPointer)) {
                return false;
            }
            $configPointer = $configPointer[$arg];
        }

        return true;
    }

    private static function getValue(array $configPointer, array $argv)
    {
        foreach ($argv as $arg) {
            $configPointer = $configPointer[$arg];
        }

        return $configPointer;
    }

    public function v()
    {
        $argv = func_get_args();
        $argc = count($argv);

        // need at least one parameter
        if (0 === $argc) {
            throw new InvalidArgumentException('no configuration field requested');
        }

        // first parameter must be string
        if (!is_string($argv[0])) {
            throw new InvalidArgumentException('first argument must be string');
        }

        // if config key exists, return its value
        $configValues = self::configValues($argv);
        if (self::configExists($this->config, $configValues)) {
            return self::getValue($this->config, $configValues);
        }

        // if it is required and not available throw error
        if (self::isRequired($argv)) {
            throw new RuntimeException('configuration value not found');
        }

        // return the default value
        return self::defaultValue($argv);
    }
}
