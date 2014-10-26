[![Build Status](https://travis-ci.org/fkooman/php-lib-ini.svg?branch=master)](https://travis-ci.org/fkooman/php-lib-ini)

# Introduction
Simple library for reading INI-style configuration files.

# Use
You can include the library using composer by requiring `fkooman/ini` in your
`composer.json`.

# API
You can initialize the `IniReader` object like this:

    $iniReader = new IniReader::fromFile('config.ini');

# Examples
Imagine the following INI file:

    foo = bar
    [one]
    xyz = abc

    ; comment
    [two]
    bar = foo

    list[] = one
    list[] = "two"
    list[] = 'three'

The following calls will provide the results mentioned in the comment:

    $iniReader->v('foo')         // returns 'bar'
    $iniReader->v('one', 'xyz')  // returns 'abc'
    $iniReader->v('two', 'list') // returns array('one', 'two', 'three')

The second last and last parameter can be used to specify whether or not the
config value is required, and if not what the default value is. By default
the key must exist otherwise the `RuntimeException` is thrown.

    $iniReader->v('def', false)                      // returns null
    $iniReader->v('abc', 'def', 'ghi', false, 'foo') // returns 'foo'
    
# License
Licensed under the Apache License, Version 2.0;

   http://www.apache.org/licenses/LICENSE-2.0
