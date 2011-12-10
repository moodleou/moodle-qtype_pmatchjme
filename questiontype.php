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
 * @package    qtype
 * @subpackage pmatch
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/pmatch/questiontype.php');


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
        $question->allowsubscript = 0;
        $question->allowsuperscript = 0;
        $question->forcelength = 0;
        $question->applydictionarycheck = 0;
        $question->extenddictionary = '';
        $question->converttospace = '';
        if (!empty($question->id)) {
            $answerids = $DB->get_records_menu('question_answers', array('question' => $question->id), '', 'id, 1');
            list ($sql, $params) = $DB->get_in_or_equal(array_keys($answerids));
            $DB->delete_records_select('qtype_pmatchjme_answers', "answerid $sql", $params);
        }
        return parent::save_question_options($question);
    }
    public function save_hints($formdata, $withparts = false) {
        parent::save_hints($formdata, true);
    }
    protected function extra_answer_fields() {
        return array('qtype_pmatchjme_answers', 'atomcount');
    }
    public function save_extra_answer_data($question, $key, $answerid) {
        global $DB;
        $extraanswerdata = new stdClass();
        $extraanswerdata->answerid = $answerid;
        $extraanswerdata->atomcount = $question->atomcount[$key];
        $DB->insert_record('qtype_pmatchjme_answers', $extraanswerdata);
    }
}
