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
 * Test helpers for the pattern match with JME question type.
 *
 * @package    qtype_pmatchjme
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test helper class for the pattern match with JME question type.
 *
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme_test_helper extends question_test_helper {

    #[\Override]
    public function get_test_questions() {
        return ['benzene'];
    }

    /**
     * Create a test question instance for drawing a benzene molecule.
     *
     * @return qtype_pmatchjme_question The prepared question object.
     */
    public function make_pmatchjme_question_benzene() {
        question_bank::load_question_definition_classes('pmatchjme');
        $question = new qtype_pmatchjme_question();

        test_question_maker::initialise_a_question($question);

        $question->name = 'Draw benzene';
        $question->questiontext = 'Please draw a benzene molecule.';
        $question->generalfeedback = 'This is the simplest aromatic molecule.';
        $question->qtype = question_bank::get_qtype('pmatchjme');
        $question->allowsubscript = false;
        $question->allowsuperscript = false;
        $question->quotematching = 0;

        $question->answers = [
            1 => new qtype_pmatchjme_answer(1, 'match (c1ccccc1)', 1, 'Well done!', FORMAT_HTML, 0),
            2 => new qtype_pmatchjme_answer(2, '*', 0, 'That is not right.', FORMAT_HTML, 1),
        ];

        return $question;
    }

    /**
     * Get form data for creating a benzene pmatchjme question.
     *
     * @return stdClass data to create a pmatchjme question.
     */
    public function get_pmatchjme_question_form_data_benzene() {
        $fromform = new stdClass();

        $fromform->name = 'Draw benzene';
        $fromform->questiontext = ['text' => 'Please draw a benzene molecule.', 'format' => FORMAT_HTML];
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = ['text' => 'This is the simplest aromatic molecule.', 'format' => FORMAT_HTML];
        $fromform->allowsubscript = 0;
        $fromform->allowsuperscript = 0;
        $fromform->modelanswer = 'c1ccccc1';
        $fromform->quotematching = 0;
        $fromform->synonymsdata = [];

        $fromform->answer = ['match (c1ccccc1)'];
        $fromform->fraction = ['1'];
        $fromform->feedback = [
            ['text' => 'Well done!', 'format' => FORMAT_HTML],
        ];
        $fromform->atomcount = ['0'];

        $fromform->otherfeedback = ['text' => 'That is not right.', 'format' => FORMAT_HTML];
        $fromform->atomcount_other = '1';
        $fromform->penalty = 0.3333333;

        return $fromform;
    }

}
