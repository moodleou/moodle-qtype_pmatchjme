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
 * PMatch JME question renderer class.
 *
 * @package    qtype
 * @subpackage pmatch
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/pmatch/renderer.php');

/**
 * Generates the output for pmatchjme questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme_renderer extends qtype_pmatch_renderer {
    public function formulation_and_controls(question_attempt $qa,
                                             question_display_options $options) {
        global $CFG;

        $question = $qa->get_question();

        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
        }

        $toreplaceid = 'applet'.$qa->get_slot();
        $toreplace = html_writer::tag('span',
                                      get_string('enablejavascript', 'qtype_pmatchjme'),
                                      array('id' => $toreplaceid));

        if ($placeholder) {
            $toreplace = html_writer::tag('span',
                                      get_string('enablejavascript', 'qtype_pmatchjme'),
                                      array('class' => 'ablock'));
            $questiontext = substr_replace($questiontext,
                                            $toreplace,
                                            strpos($questiontext, $placeholder),
                                            strlen($placeholder));
        }

        $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        if (!$placeholder) {
            $answerlabel = html_writer::tag('span', get_string('answer', 'qtype_pmatch', ''),
                                            array('class' => 'answerlabel'));
            $result .= html_writer::tag('div', $answerlabel.$toreplace, array('class' => 'ablock'));
        }

        if ($qa->get_state() == question_state::$invalid) {
            $lastresponse = $this->get_last_response($qa);
            $result .= html_writer::nonempty_tag('div',
                                                $question->get_validation_error($lastresponse),
                                                array('class' => 'validationerror'));
        }
        $result .= html_writer::tag('div',
                                    $this->hidden_fields($qa),
                                    array('class' => 'inputcontrol'));

        $this->require_js($toreplaceid, $qa, $options->readonly, $options->correctness,
                                                                $question->allowsubscript, $question->allowsuperscript);

        // Include JSME loader script as an html tag.
        $jsmescript = $CFG->wwwroot.'/question/type/pmatchjme/jsme/jsme.nocache.js';
        $result .= html_writer::tag('script', '', array('src' => $jsmescript));

        return $result;
    }
    protected function require_js($toreplaceid, question_attempt $qa, $readonly, $correctness, $nostereo, $autoez) {
        global $PAGE;
        $jsmodule = array(
            'name'     => 'qtype_pmatchjme',
            'fullpath' => '/question/type/pmatchjme/module.js',
            'requires' => array(),
            'strings' => array(
                array('enablejavascript', 'qtype_pmatchjme')
            )
        );
        $topnode = 'div.que.pmatchjme#q'.$qa->get_slot();
        $appleturl = new moodle_url('/question/type/pmatchjme/jme/JME.jar');
        if ($correctness) {
            $feedbackimage = $this->feedback_image($this->fraction_for_last_response($qa));
        } else {
            $feedbackimage = '';
        }
        $name = 'JME'.$qa->get_slot();
        $appletid = 'jme'.$qa->get_slot();
        $PAGE->requires->js_init_call('M.qtype_pmatchjme.insert_applet',
                                      array($toreplaceid,
                                            $name,
                                            $appletid,
                                            $topnode,
                                            $appleturl->out(),
                                            $feedbackimage,
                                            $readonly,
                                            (bool)$nostereo,
                                            (bool)$autoez),
                                      false,
                                      $jsmodule);

    }

    protected function hidden_fields(question_attempt $qa) {
        $question = $qa->get_question();

        $hiddenfieldshtml = '';
        $inputids = new stdClass();
        $responsefields = array_keys($question->get_expected_data());
        foreach ($responsefields as $responsefield) {
            $hiddenfieldshtml .= $this->hidden_field_for_qt_var($qa, $responsefield);
        }
        return $hiddenfieldshtml;
    }
    protected function hidden_field_for_qt_var(question_attempt $qa, $varname) {
        $value = $qa->get_last_qt_var($varname, '');
        $fieldname = $qa->get_qt_field_name($varname);
        $attributes = array('type' => 'hidden',
                            'id' => str_replace(':', '_', $fieldname),
                            'class' => $varname,
                            'name' => $fieldname,
                            'value' => $value);
        return html_writer::empty_tag('input', $attributes);
    }

    protected function fraction_for_last_response(question_attempt $qa) {
        $question = $qa->get_question();
        $lastresponse = $this->get_last_response($qa);
        $answer = $question->get_matching_answer($lastresponse);
        if ($answer) {
            $fraction = $answer->fraction;
        } else {
            $fraction = 0;
        }
        return $fraction;
    }


    protected function get_last_response(question_attempt $qa) {
        $question = $qa->get_question();
        $responsefields = array_keys($question->get_expected_data());
        $response = array();
        foreach ($responsefields as $responsefield) {
            $response[$responsefield] = $qa->get_last_qt_var($responsefield);
        }
        return $response;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer($this->get_last_response($qa));
        if (!$answer) {
            return '';
        }

        $feedback = '';
        if ($answer->feedback) {
            $feedback .= $question->format_text($answer->feedback, $answer->feedbackformat,
                    $qa, 'question', 'answerfeedback', $answer->id);
        }
        if ($answer->atomcount) {
            $response = $this->get_last_response($qa);
            $atomcountfeedbacks = $question->check_atom_count($response);
            if (count($atomcountfeedbacks) > 1) {
                $listitems = '';
                foreach ($atomcountfeedbacks as $atomcountfeedback) {
                    $listitems .= html_writer::tag('li', $atomcountfeedback);
                }
                $ul = html_writer::tag('ul', $listitems);
                $feedback .= html_writer::tag('div', $ul, array('class' => 'atomcountfeedback'));
            } else if (count($atomcountfeedbacks) === 1) {
                $feedbackitem = array_shift($atomcountfeedbacks);
                $feedback .= html_writer::tag('div', $feedbackitem,
                                                            array('class' => 'atomcountfeedback'));
            }
        }

        return $feedback;
    }


}
