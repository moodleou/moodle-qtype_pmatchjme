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
 * @package    qtype_pmatchjme
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/pmatch/renderer.php');

/**
 * Generates the output for pmatchjme questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme_renderer extends qtype_pmatch_renderer {

    #[\Override]
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {

        $question = $qa->get_question();

        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
        }

        $containerid = 'qtype_pmatchjme-applet' . $qa->get_slot();
        $jsmecontainer = html_writer::tag('div',
                get_string('enablejavascript', 'qtype_pmatchjme'),
                ['id' => $containerid, 'class' => 'qtype_pmatchjme-applet-warning']);

        if ($placeholder) {
            $questiontext = substr_replace($questiontext, $jsmecontainer,
                    strpos($questiontext, $placeholder), strlen($placeholder));
        }

        $result = html_writer::tag('div', $questiontext, ['class' => 'qtext']);

        if (!$placeholder) {
            $answerlabel = html_writer::tag('span', get_string('answer', 'qtype_pmatch', ''),
                    ['class' => 'answerlabel']);
            $result .= html_writer::tag('div', $answerlabel . $jsmecontainer, ['class' => 'ablock']);
        }

        if ($qa->get_state() == question_state::$invalid) {
            $lastresponse = $this->get_last_response($qa);
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($lastresponse),
                    ['class' => 'validationerror']);
        }

        $result .= html_writer::tag('div', $this->hidden_fields($qa), ['class' => 'inputcontrol']);

        $this->require_js($containerid, $qa, $options->readonly, $options->correctness,
                $question->allowsubscript, $question->allowsuperscript);

        return $result;
    }

    /**
     * Load required JavaScript for the question applet.
     *
     * @param string $containerid The HTML container ID.
     * @param question_attempt $qa The question attempt.
     * @param bool $readonly Whether the applet is read-only.
     * @param bool $correctness Whether to show correctness feedback.
     * @param int $nostereo 0 or 1, whether to disable stereo mode.
     * @param int $autoez 0 or 1, whether to enable auto-easy mode.
     */
    protected function require_js(string $containerid, question_attempt $qa, bool $readonly,
                                  bool   $correctness, int $nostereo, int $autoez): void {
        if ($correctness) {
            $feedbackimage = $this->feedback_image($this->fraction_for_last_response($qa));
        } else {
            $feedbackimage = '';
        }
        $this->page->requires->js_call_amd('qtype_pmatchjme/jsme', 'insertApplet',
            [$containerid, $qa->get_outer_question_div_unique_id(),
                $feedbackimage, $readonly, (bool)$nostereo, (bool)$autoez]);
    }

    /**
     * Build hidden input fields for all expected response data.
     *
     * @param question_attempt $qa The question attempt.
     * @return string HTML for hidden fields.
     */
    protected function hidden_fields(question_attempt $qa): string {
        $question = $qa->get_question();

        $hiddenfieldshtml = '';
        $inputids = new stdClass();
        $responsefields = array_keys($question->get_expected_data());
        foreach ($responsefields as $responsefield) {
            $hiddenfieldshtml .= $this->hidden_field_for_qt_var($qa, $responsefield);
        }
        return $hiddenfieldshtml;
    }

    /**
     * Create a hidden input field for a specific question attempt variable.
     *
     * @param question_attempt $qa The question attempt.
     * @param string $varname The variable name.
     * @return string HTML for the hidden input field.
     */
    protected function hidden_field_for_qt_var(question_attempt $qa, string $varname): string {
        $value = $qa->get_last_qt_var($varname, '');
        $fieldname = $qa->get_qt_field_name($varname);
        $attributes = [
            'type' => 'hidden',
            'id' => str_replace(':', '_', $fieldname),
            'class' => $varname,
            'name' => $fieldname,
            'value' => $value,
        ];
        return html_writer::empty_tag('input', $attributes);
    }

    /**
     * Get the fraction (score) for the last response.
     *
     * @param question_attempt $qa The question attempt.
     * @return float|int The fraction score.
     */
    protected function fraction_for_last_response(question_attempt $qa): float|int {
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

    /**
     * Get the last saved response values for all expected fields.
     *
     * @param question_attempt $qa The question attempt.
     * @return array The last response data.
     */
    protected function get_last_response(question_attempt $qa): array {
        $question = $qa->get_question();
        $responsefields = array_keys($question->get_expected_data());
        $response = [];
        foreach ($responsefields as $responsefield) {
            $response[$responsefield] = $qa->get_last_qt_var($responsefield);
        }
        return $response;
    }

    #[\Override]
    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        $response = $this->get_last_response($qa);
        $answer = $question->get_matching_answer($response);
        if (!$answer || !isset($response['answer'])) {
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
                $feedback .= html_writer::tag('div', $ul, ['class' => 'atomcountfeedback']);
            } else if (count($atomcountfeedbacks) === 1) {
                $feedbackitem = array_shift($atomcountfeedbacks);
                $feedback .= html_writer::tag('div', $feedbackitem,
                    ['class' => 'atomcountfeedback']);
            }
        }

        return $feedback;
    }
}
