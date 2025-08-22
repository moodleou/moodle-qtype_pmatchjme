<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the pmatchjme student response parsing and use of pmatch for SMILES strings.
 *
 * @package   qtype_pmatchjme
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_pmatchjme;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/type/pmatch/pmatchlib.php');

/**
 * Unit tests for the pmatchjme response parsing and use of pmatch for SMILES strings.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group     qtype_pmatchjme
 * @covers \pmatch_expression
 * @covers \pmatch_options
 * @covers \pmatch_parsed_string
 */
final class chemicalexpressionmatching_test extends \basic_testcase {

    /**
     * Check if a string matches a given pmatch expression.
     *
     * @param string $string The input string.
     * @param string $expression The pattern expression.
     * @param mixed|null $options Optional parsing or matching options.
     * @return bool True if matched, false otherwise.
     */
    protected function match(string $string, string $expression, mixed $options = null): bool {
        $string = new \pmatch_parsed_string($string, $options);
        $expression = new \pmatch_expression($expression, $options);
        return $expression->matches($string);
    }

    /**
     * Test parsing of a SMILES string using pmatch_parsed_string.
     */
    public function test_pmatch_parse_string(): void {
        $options = new \pmatch_options();

        $parsedstring = new \pmatch_parsed_string('CC(=O)O', $options);
        $this->assertEquals($parsedstring->get_words(), ['CC(=O)O']);

        $parsedstring = new \pmatch_parsed_string('CC2COc1ccccc1N2C(=O)C(Cl)Cl', $options);
        $this->assertEquals($parsedstring->get_words(), ['CC2COc1ccccc1N2C(=O)C(Cl)Cl']);
    }

    /**
     * Test matching of SMILES strings against pmatch expressions.
     */
    public function test_pmatch_matching(): void {
        $this->assertTrue($this->match('CC(=O)O', 'match(CC\(=O\)O)'));
        $this->assertTrue($this->match('CC2COc1ccccc1N2C(=O)C(Cl)Cl',
                                        'match(CC2COc1ccccc1N2C\(=O\)C\(Cl\)Cl)'));
        // Change expression slightly.
        $this->assertFalse($this->match('CC(=O)O', 'match(C\(=O\)O)'));
        // Change response slightly.
        $this->assertFalse($this->match('C2COc1ccccc1N2C(=O)C(Cl)Cl',
                                        'match(CC2COc1ccccc1N2C\(=O\)C\(Cl\)Cl)'));
    }
}
