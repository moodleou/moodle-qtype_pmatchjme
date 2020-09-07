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
 * Question type class for the pmatch with JME editor question type.
 *
 * @package    qtype_pmatchjme
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use qtype_pmatch\local\spell\qtype_pmatch_spell_checker;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/pmatch/questiontype.php');

/**
 * Class to represent a pmatchjme question answer, loaded from the question_answers table
 * in the database.
 */
class qtype_pmatchjme_answer extends question_answer {
    /**
     * Constructor.
     * @param int $id the answer.
     * @param string $answer the answer.
     * @param int $answerformat the format of the answer.
     * @param number $fraction the fraction this answer is worth.
     * @param string $feedback the feedback for this answer.
     * @param int $feedbackformat the format of the feedback.
     * @param integer $atomcount
     */
    public function __construct($id, $answer, $fraction, $feedback, $feedbackformat, $atomcount) {
        parent::__construct($id, $answer, $fraction, $feedback, $feedbackformat);
        $this->atomcount = $atomcount;
    }
}


/**
 * The pmatchjme question type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme extends qtype_pmatch {
    public function save_question_options($question) {
        global $DB;
        $question->usecase = 1;
        $question->forcelength = 0;
        $question->applydictionarycheck = qtype_pmatch_spell_checker::DO_NOT_CHECK_OPTION;
        $question->extenddictionary = '';
        $question->converttospace = '';
        if (!empty($question->id)) {
            $this->delete_extra_answer_records($question->id);
        }
        return parent::save_question_options($question);
    }
    public function save_hints($formdata, $withparts = false) {
        parent::save_hints($formdata, true);
    }
    public function extra_answer_fields() {
        return array('qtype_pmatchjme_answers', 'atomcount');
    }
    public function save_extra_answer_data($question, $key, $answerid) {
        global $DB;
        $extraanswerdata = new stdClass();
        $extraanswerdata->answerid = $answerid;
        if ($key === 'other') {
            $extraanswerdata->atomcount = $question->atomcount_other;
        } else {
            $extraanswerdata->atomcount = $question->atomcount[$key];
        }
        $DB->insert_record('qtype_pmatchjme_answers', $extraanswerdata);
    }
    /**
     * Initialise question_definition::answers field.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     * @param bool $forceplaintextanswers most qtypes assume that answers are
     *      FORMAT_PLAIN, and dont use the answerformat DB column (it contains
     *      the default 0 = FORMAT_MOODLE). Therefore, by default this method
     *      ingores answerformat. Pass false here to use answerformat. For example
     *      multichoice does this.
     */
    protected function initialise_question_answers(question_definition $question,
            $questiondata, $forceplaintextanswers = true) {
        $question->answers = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id] = new qtype_pmatchjme_answer($a->id, $a->answer,
                    $a->fraction, $a->feedback, $a->feedbackformat, $a->atomcount);
            if (!$forceplaintextanswers) {
                $question->answers[$a->id]->answerformat = $a->answerformat;
            }
        }
    }
    public function delete_question($questionid, $contextid) {
        $this->delete_extra_answer_records($questionid);
        parent::delete_question($questionid, $contextid);
    }

    protected function delete_extra_answer_records($questionid) {
        global $DB;
        $answerids = $DB->get_records_menu('question_answers',
                                           array('question' => $questionid),
                                           '', 'id, 1');
        if (count($answerids) != 0) {
            list ($sql, $params) = $DB->get_in_or_equal(array_keys($answerids));
            $DB->delete_records_select('qtype_pmatchjme_answers', "answerid $sql", $params);
        }
    }
}