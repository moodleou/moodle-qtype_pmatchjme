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
 * Restore qtype pmatchjme plugin.
 *
 * @package   qtype_pmatchjme
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restore plugin class that provides the necessary information
 * needed to restore one pmatchjme qtype plugin.
 *
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_pmatchjme_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level.
     */
    protected function define_question_plugin_structure() {

        $paths = [];

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'pmatch';
        $elepath = $this->get_pathfor('/pmatchjme'); // We used get_recommended_name() so this works.
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'pmatchjme_answer';
        $elepath = $this->get_pathfor('/pmatchjme_answers/pmatchjme_answer'); // We used get_recommended_name() so this works.
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'synonym';
        $elepath = $this->get_pathfor('/synonyms/synonym');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    #[\Override]
    public static function convert_backup_to_questiondata(array $backupdata): \stdClass {
        $questiondata = parent::convert_backup_to_questiondata($backupdata);
        $qtype = $questiondata->qtype;
        if (isset($backupdata["plugin_qtype_{$qtype}_question"]['pmatchjme'])) {
            $questiondata->options = (object) array_merge(
                (array) $questiondata->options,
                $backupdata["plugin_qtype_{$qtype}_question"]['pmatchjme'][0],
            );
        }

        if (isset($backupdata["plugin_qtype_{$qtype}_question"]['synonyms']['synonym'])) {
            $questiondata->options->synonyms = [];
            foreach ($backupdata["plugin_qtype_{$qtype}_question"]['synonyms']['synonym'] as $synonym) {
                $questiondata->options->synonyms[] = (object) $synonym;
            }
        }
        if (isset($backupdata["plugin_qtype_{$qtype}_question"]['pmatchjme_answers']['pmatchjme_answer'])) {
            foreach ($backupdata["plugin_qtype_{$qtype}_question"]['pmatchjme_answers']['pmatchjme_answer'] as $pmatchjmeanswer) {
                foreach ($questiondata->options->answers as &$answer) {
                    if ($answer->id == $pmatchjmeanswer['answerid']) {
                        $answer->atomcount = $pmatchjmeanswer['atomcount'];
                        continue 2;
                    }
                }
            }
        }

        return $questiondata;
    }

    #[\Override]
    protected function define_excluded_identity_hash_fields(): array {
        // Those field are covered by pmatch.
        return [
            'options/synonyms/id',
            'options/synonyms/questionid',
            'options/responsetemplate',
            'options/sentencedividers',
            'options/modelanswer',
            'options/quotematching',
        ];
    }

    #[\Override]
    public static function remove_excluded_question_data(stdClass $questiondata, array $excludefields = []): stdClass {
        // Option responsetemplate default is null, we need to remove it completely.
        unset($questiondata->options->responsetemplate);
        return parent::remove_excluded_question_data($questiondata, $excludefields);
    }

    /**
     * Process the qtype/pmatch element.
     *
     * @param array $data the data from the backup file.
     */
    public function process_pmatch(array $data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its qtype_pmatch too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->questionid = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record('qtype_pmatch', $data);
            // Create mapping.
            $this->set_mapping('qtype_pmatch', $oldid, $newitemid);
        }
    }


    /**
     * Process the qtype/varnumericset_answer element.
     *
     * @param array $data the data from the backup file.
     */
    public function process_pmatchjme_answer(array $data) {
        global $DB;

        $data = (object)$data;

        $data->answerid = $this->get_mappingid('question_answer', $data->answerid);

        // Detect if the question is created.
        $oldquestionid   = $this->get_old_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;
        if ($questioncreated) {
            // Insert record.
            $newitemid = $DB->insert_record('qtype_pmatchjme_answers', $data);
            // Create mapping.
            $this->set_mapping('qtype_pmatchjme_answers', $data->id, $newitemid);
        }
    }

    /**
     * Process the qtype/synonyms/synonym element.
     *
     * @param array $data the data from the backup file.
     */
    public function process_synonym(array $data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore,
        // we need to create its qtype_pmatch_synonyms too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->questionid = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record('qtype_pmatch_synonyms', $data);
        }
    }

}
