<?php

/**
 * Copyright 2013 François Kooman <fkooman@tuxed.net>
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

use PHPUnit_Framework_TestCase;

class IniReaderTest extends PHPUnit_Framework_TestCase
{
    public function testRootLevel()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('bar', $reader->v(array('foo')));
    }

    public function testSubLevel()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('abc', $reader->v(array('one', 'xyz')));
    }

    public function testSubLevelArray()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals(array('one', 'two', 'three'), $reader->v(array('two', 'list')));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage configuration value not found
     */
    public function testMissingValueRequired()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('foo', $reader->v(array('one', 'two', 'three')));
    }

    public function testMissingValueNotRequiredNullDefault()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertNull($reader->v(array('one', 'two', 'three', false)));
    }

    public function testMissingValueNotRequiredWithDefault()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('foobar', $reader->v(array('one', 'two', 'three', false, 'foobar')));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage configuration value not found
     */
    public function testDeeperLevelNotExisting()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $reader->v(array('foo', 'bar'));
    }

    public function testDeeperLevelNotExistingWithDefaultValue()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('xyz', $reader->v(array('foo', 'bar', false, 'xyz')));
    }

    public function testKeyedSection()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals(array('xyz' => 'abc'), $reader->v(array('one')));
    }

    public function testExplicitRequired()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('abc', $reader->v(array('one', 'xyz', true)));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage configuration value not found
     */
    public function testExplicitRequiredMissing()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('foo', $reader->v(array('one', 'two', 'three', true)));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage configuration value not found
     */
    public function testExplicitRequiredMissingWithDefaultValue()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $this->assertEquals('foo', $reader->v(array('one', 'two', 'three', true, 'useless')));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage no configuration field requested
     */
    public function testNoParameters()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $reader->v(array());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage no configuration field requested
     */
    public function testNoParametersOnlyBool()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $reader->v(array(true));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage only strings can be used as configuration keys
     */
    public function testNonStringParameter()
    {
        $reader = IniReader::fromFile('tests/data/simple.ini');
        $reader->v(array('one', 'two', 5, false, 'foobar'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage unable to read configuration file
     */
    public function testMissingIniFile()
    {
        $c = IniReader::fromFile('missing.txt');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage unable to parse configuration file
     */
    public function testBrokenIniFile()
    {
        $c = IniReader::fromFile('tests/data/raw.dat');
    }
}
