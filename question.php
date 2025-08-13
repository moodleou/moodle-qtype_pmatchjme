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
 * Short answer question definition class.
 *
 * @package    qtype_pmatchjme
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/pmatch/question.php');

/**
 * Represents a pmatchjme question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchjme_question extends qtype_pmatch_question {

    /** @var bool whether answers should be graded case-sensitively. */
    public $usecase;

    #[\Override]
    public function get_expected_data() {
        return ['answer' => PARAM_RAW, 'jme' => PARAM_RAW, 'mol' => PARAM_RAW];
    }

    #[\Override]
    public function get_correct_response() {
        $response = parent::get_correct_response();
        if (!$response) {
            return $response;
        }

        $matches = [];
        $pmatchanswer = preg_replace('!\s!', '', $response['answer']);
        if (preg_match('!match\((.+)\)$!iA', $pmatchanswer, $matches)) {
            return ['answer' => $matches[1]];
        } else {
            return null;
        }
    }

    /**
     * Check if the atom count in the response matches the correct answer.
     *
     * @param array $response The student's response data.
     * @return array List of feedback messages.
     */
    public function check_atom_count(array $response): array {
        $correctresponse = $this->get_correct_response();
        if (!isset($correctresponse['answer'])) {
            // We don't really have correct answer.
            return [];
        }
        $pmatchanswer = $correctresponse['answer'];
        $answerparts = $this->count_compound_parts($pmatchanswer);

        $smilesresponse = preg_replace('!\s!', '', $response['answer']);
        $responseparts = $this->count_compound_parts($smilesresponse);

        $messages = [];
        $anyerror = (count($answerparts) != count($responseparts));
        $i = 0;
        foreach ($responseparts as $part => $responsecount) {
            $answercount = isset($answerparts[$part]) ? $answerparts[$part] : 0;
            list($messages[$i], $errorhere) = $this->part_comparison($answercount,
                                                                       $responsecount,
                                                                       $part);
            $i++;
            $anyerror = $anyerror || $errorhere;
        }

        if (!$anyerror) {
            // Count of smiles elements for student response and correct answer is the same
            // clear all other messages and replace it with this one.
            $messages = [get_string('smilescorrectcount', 'qtype_pmatchjme')];
        }
        return $messages;
    }

    /**
     * Compare the count of a specific part between correct answer and response.
     *
     * @param int $answercount Count of the part in the correct answer.
     * @param int $responsecount Count of the part in the student's response.
     * @param string $part The part identifier.
     * @return array Array containing the message (string) and error flag (bool).
     */
    protected function part_comparison(int $answercount, int $responsecount, string $part): array {
        $humanreadablepart = $this->get_part_name($part);
        if ($answercount < $responsecount) {
            $message = get_string('smilestoomany', 'qtype_pmatchjme', $humanreadablepart);
            $error = true;
        } else if ($answercount > $responsecount) {
            $message = get_string('smilestoofew', 'qtype_pmatchjme', $humanreadablepart);
            $error = true;
        } else {
            $message = get_string('smilesequal', 'qtype_pmatchjme', $humanreadablepart);
            $error = false;
        }
        return [$message, $error];
    }

    /**
     * Get part name.
     *
     * @param string $part symbol for part of a molecule.
     * @return string the human-readable name for that part.
     */
    protected function get_part_name($part) {
        switch ($part) {
            case '=':
                return get_string('smiles_doublebond', 'qtype_pmatchjme');
            case '#':
                return get_string('smiles_triplebond', 'qtype_pmatchjme');
            case 'c':
                return get_string('smiles_aromatic_c', 'qtype_pmatchjme');
            default:
                return get_string('smiles_' . strtolower($part), 'qtype_pmatchjme');
        }
    }

    /**
     * Count parts of compound.
     *
     * @param string $compound The compound to analyse.
     * @return array The number of parts in the compound.
     */
    protected function count_compound_parts($compound): array {
        $partstocount = ['Cl', 'Br', 'c', 'C', 'O', 'N', 'S', 'F', 'I', '=', '#'];
        $cursor = 0;
        $count = [];
        while ($cursor < strlen($compound)) {
            $counted = false;
            foreach ($partstocount as $parttocount) {
                if ($parttocount == substr($compound, $cursor, strlen($parttocount))) {
                    $cursor = $cursor + strlen($parttocount);
                    if (!isset($count[$parttocount])) {
                        $count[$parttocount] = 1;
                    } else {
                        $count[$parttocount]++;
                    }
                    $counted = true;
                    break;
                }
            }
            if (!$counted) {
                $cursor++;
            }
        }
        return $count;
    }

    #[\Override]
    public function start_attempt(question_attempt_step $step, $variant) {
    }

    #[\Override]
    public function apply_attempt_state(question_attempt_step $step) {
    }
}
