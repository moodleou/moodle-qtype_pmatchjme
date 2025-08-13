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

namespace qtype_pmatchjme;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the pmatchjme walkthrough.
 *
 * @package qtype_pmatchjme
 * @copyright 2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_pmatchjme_question
 */
final class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    /**
     * Test attempt question with mode deferredfeedback.
     */
    public function test_deferred_feedback_unanswered(): void {

        $this->setAdminUser();
        $q = \test_question_maker::make_question('pmatchjme');
        $q->contextid = \context_system::instance()->id;
        $this->start_attempt_at_question($q, 'deferredfeedback', 2);

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation());
        $this->process_submission(['answer' => '']);
        $this->finish();
        // Verify.
        $this->check_output_does_not_contain('atomcountfeedback');
    }
}
