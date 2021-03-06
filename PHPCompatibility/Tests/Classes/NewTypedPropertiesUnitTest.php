<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Classes;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewTypedProperties sniff.
 *
 * @group newTypedProperties
 * @group classes
 *
 * @covers \PHPCompatibility\Sniffs\Classes\NewTypedPropertiesSniff
 *
 * @since 9.2.0
 */
class NewTypedPropertiesUnitTest extends BaseSniffTest
{

    /**
     * testNewTypedProperties
     *
     * @dataProvider dataNewTypedProperties
     *
     * @param array $line            The line number on which the error should occur.
     * @param bool  $testNoViolation Whether or not to test noViolation for PHP 7.4.
     *
     * @return void
     */
    public function testNewTypedProperties($line, $testNoViolation = false)
    {
        $file = $this->sniffFile(__FILE__, '7.3');
        $this->assertError($file, $line, 'Typed properties are not supported in PHP 7.3 or earlier');

        if ($testNoViolation === true) {
            $file = $this->sniffFile(__FILE__, '7.4');
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testNewTypedProperties()
     *
     * @return array
     */
    public function dataNewTypedProperties()
    {
        return [
            [23, true],
            [24, true],
            [25, true],
            [28, true],
            [31, true],
            [34, true],
            [35, true],
            [38, true],
            [41, true],
            [49, true],
            [51, true],
            [54, true],
            [57, true],
            [62],
            [63],
            [64],
            [65],
            [66],
            [71],
            [74],
        ];
    }


    /**
     * Verify the sniff doesn't throw false positives for non-typed properties.
     *
     * @return void
     */
    public function testNoFalsePositivesNewTypedProperties()
    {
        $file = $this->sniffFile(__FILE__, '7.3');

        for ($line = 1; $line < 19; $line++) {
            $this->assertNoViolation($file, $line);
        }
    }


    /**
     * testInvalidPropertyType
     *
     * @dataProvider dataInvalidPropertyType
     *
     * @param array  $line The line number on which the error should occur.
     * @param string $type The invalid type which should be detected.
     *
     * @return void
     */
    public function testInvalidPropertyType($line, $type)
    {
        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertError($file, $line, "$type is not supported as a type declaration for properties");
    }

    /**
     * Data provider.
     *
     * @see testInvalidPropertyType()
     *
     * @return array
     */
    public function dataInvalidPropertyType()
    {
        return [
            [62, 'void'],
            [63, 'callable'],
            [64, 'callable'],
            [65, 'boolean'],
            [66, 'integer'],
        ];
    }


    /**
     * Test correctly throwing an error when types are used which were not available on a particular PHP version.
     *
     * @dataProvider dataNewTypedPropertyTypes
     *
     * @param string $type              The declaration type.
     * @param string $lastVersionBefore The PHP version just *before* the type hint was introduced.
     * @param array  $line              The line number where the error is expected.
     * @param string $okVersion         A PHP version in which the type hint was ok to be used.
     * @param bool   $testNoViolation   Whether or not to test noViolation.
     *                                  Defaults to true.
     *
     * @return void
     */
    public function testNewTypedPropertyTypes($type, $lastVersionBefore, $line, $okVersion, $testNoViolation = true)
    {
        $file = $this->sniffFile(__FILE__, $lastVersionBefore);
        $this->assertError($file, $line, "'{$type}' property type is not present in PHP version {$lastVersionBefore} or earlier");

        if ($testNoViolation === true) {
            $file = $this->sniffFile(__FILE__, $okVersion);
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testNewTypedPropertyTypes()
     *
     * @return array
     */
    public function dataNewTypedPropertyTypes()
    {
        return [
            ['mixed', '7.4', 71, '8.0'],
            ['mixed', '7.4', 74, '8.0', false],
        ];
    }


    /**
     * Verify an error is thrown for nullable mixed types.
     *
     * @return void
     */
    public function testInvalidNullableMixed()
    {
        $file = $this->sniffFile(__FILE__, '8.0');
        $this->assertError($file, 74, 'Mixed types cannot be nullable, null is already part of the mixed type');
    }


    /**
     * Test no false positives for non-nullable "mixed" type.
     *
     * @return void
     */
    public function testInvalidNullableMixedNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '8.0');
        $this->assertNoViolation($file, 71);
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will also throw warnings/errors
     * about invalid typed properties.
     */
}
