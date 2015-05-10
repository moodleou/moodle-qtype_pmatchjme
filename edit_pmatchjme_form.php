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
 * Defines the editing form for the pmatch question type.
 *
 * @package    qtype
 * @subpackage pmatchjme
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/pmatch/edit_pmatch_form.php');

/**
 * pmatchjme question editing form definition.
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme_edit_form extends qtype_pmatch_edit_form {
    public function qtype() {
        return 'pmatchjme';
    }

    protected function general_answer_fields($mform) {
        $mform->addElement('header', 'generalheader', get_string('answeringoptions', 'qtype_pmatchjme'));
        $mform->addElement('advcheckbox', 'allowsuperscript', '', get_string('autoez', 'qtype_pmatchjme'));
        $mform->setDefault('allowsuperscript', '1');
        $mform->addElement('advcheckbox', 'allowsubscript', '', get_string('nostereo', 'qtype_pmatchjme'));
        $mform->setDefault('allowsubscript', '0');
    }

    protected function get_per_answer_fields($mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions, $repeatedoptions, $answersoption);
        $repeated[] = $mform->createElement('advcheckbox', 'atomcount', '',
                                                        get_string('atomcount', 'qtype_pmatchjme'));
        return $repeated;
    }

    protected function add_other_answer_fields($mform) {
        parent::add_other_answer_fields($mform);
        $mform->addElement('advcheckbox', 'atomcount_other', '', get_string('atomcount', 'qtype_pmatchjme'));
    }

    protected function data_preprocessing_hints($question, $withclearwrong = false,
                                                $withshownumpartscorrect = false) {
        $withclearwrong = true;
        return parent::data_preprocessing_hints($question, $withclearwrong,
                                                                        $withshownumpartscorrect);
    }

    protected function get_hint_fields($withclearwrong = false, $withshownumpartscorrect = false) {
        list($repeated, $repeatedoptions) = parent::get_hint_fields(false, false);

        $mform = $this->_form;
        $repeated[] = $mform->createElement('advcheckbox', 'hintclearwrong',
                                            get_string('options', 'question'),
                                            get_string('allowanothertry', 'qtype_pmatchjme'));
        return array($repeated, $repeatedoptions);
    }

    /**
     * Perform the necessary preprocessing for the fields added by
     * {@link add_per_answer_fields()}.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_answers($question, $withanswerfiles = false) {
        $question = parent::data_preprocessing_answers($question);
        if (empty($question->options->answers)) {
            return $question;
        }
        $key = 0;
        foreach ($question->options->answers as $answer) {
            $question->atomcount[$key] = $answer->atomcount;
            $key++;
        }
        return $question;
    }

    protected function data_preprocessing_other_answer($question) {
        if ($this->otheranswer) {
            if (!isset($question->atomcount)) {
                $question->atomcount = array();
            }
            $question->atomcount_other = $this->otheranswer->atomcount;
        }
        $question = parent::data_preprocessing_other_answer($question);
        return $question;
    }

    protected function straight_smiles_string_match($string) {
        $ciw = '('.PMATCH_CHARACTER .'|'. '\\\\'.PMATCH_SPECIAL_CHARACTER.')';
        return (1 == preg_match("~match\({$ciw}+\)~i", $string));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        foreach ($answers as $key => $answer) {
            $nowhitespaceanswer = preg_replace('!\s!', '', $answer);
            if ($nowhitespaceanswer !== '') {
                if ($data['fraction'][$key] == 1 && !$maxgrade) {
                    $maxgrade = true;
                    if (!$this->straight_smiles_string_match($nowhitespaceanswer)) {
                        $errors["answer[$key]"]
                            = get_string('firstcorrectanswermustbestraightmatch', 'qtype_pmatchjme');
                    }
                    if (!empty($data['atomcount'][$key])) {
                        $errors["atomcount[$key]"]
                            = get_string('firstcorrectanswermustnotrequireatomcountfeedback', 'qtype_pmatchjme');
                    }
                }
            }
        }
        return $errors;
    }

    protected function place_holder_errors($questiontext, $usesub) {
        return array();
    }
}