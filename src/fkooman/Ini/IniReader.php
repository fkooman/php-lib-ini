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

    public function v()
    {
        $p = func_get_args();
        $required = true;
        $default = null;
        $maxCount = count($p);

        if (0 === $maxCount) {
            throw new InvalidArgumentException('no configuration field requested');
        }

        // find the max depth count, the (optional) required bool and (optional)
        // default value
        for ($i = 0; $i < count($p); ++$i) {
            if (is_bool($p[$i])) {
                if (0 === $i) {
                    throw new InvalidArgumentException('no configuration field requested');
                }
                $maxCount = $i;
                $required = $p[$i];
                if ($i < count($p) - 1) {
                    $default = $p[$i + 1];
                }
                // we are done, ignore the other parameters
                break;
            } else {
                if (!is_string($p[$i])) {
                    throw new InvalidArgumentException('only strings can be used as configuration keys');
                }
            }
        }

        // start at the root of the config
        $configPointer = $this->config;

        // traverse the array until the config value was found
        for ($i = 0; $i < $maxCount; ++$i) {
            if (!array_key_exists($p[$i], $configPointer)) {
                // does not exist
                if ($required) {
                    throw new RuntimeException('configuration value not found');
                }

                return $default;
            }
            // exists
            // if last, return it (could be array or string!
            if ($maxCount - 1 === $i) {
                return $configPointer[$p[$i]];
            }
            if (!is_array($configPointer[$p[$i]])) {
                // unable to go deeper, does not exist
                if ($required) {
                    throw new RuntimeException('configuration value not found');
                }

                return $default;
            }
            $configPointer = $configPointer[$p[$i]];
        }
    }
}
