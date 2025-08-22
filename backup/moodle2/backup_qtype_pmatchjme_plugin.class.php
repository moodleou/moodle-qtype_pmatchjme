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
 * Backup qtype pmatchjme plugin.
 *
 * @package   qtype_pmatchjme
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Provides the information to backup pmatchjme questions.
 *
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qtype_pmatchjme_plugin extends backup_qtype_plugin {

    /**
     * Returns the qtype information to attach to question element
     */
    protected function define_question_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', 'pmatchjme');

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        // This qtype uses standard question_answers, add them here
        // to the tree before any other information that will use them.
        $this->add_question_question_answers($pluginwrapper);

        // Extra answer fields for varnumericset question type.
        $this->add_question_qtype_pmatchjme_answers($pluginwrapper);

        // Now create the qtype own structures.
        $pmatchoptions = new backup_nested_element('pmatchjme', ['id'], ['forcelength',
            'usecase', 'converttospace', 'applydictionarycheck', 'extenddictionary',
            'allowsubscript', 'allowsuperscript']);

        $synonyms = new backup_nested_element('synonyms');

        $synonym = new backup_nested_element('synonym', ['id'], ['word', 'synonyms']);

        $pluginwrapper->add_child($pmatchoptions);
        $pluginwrapper->add_child($synonyms);
        $synonyms->add_child($synonym);

        // Set source to populate the data.
        $pmatchoptions->set_source_table('qtype_pmatch',
            ['questionid' => backup::VAR_PARENTID]);
        $synonym->set_source_table('qtype_pmatch_synonyms',
            ['questionid' => backup::VAR_PARENTID], 'id ASC');

        // Don't need to annotate ids nor files.

        return $plugin;
    }

    /**
     * Add pmatchjme answer data to the backup structure.
     *
     * @param object $element The parent backup element.
     * @throws backup_step_exception If the given element is not a backup_nested_element.
     */
    protected function add_question_qtype_pmatchjme_answers($element): void {
        // Check $element is one nested_backup_element.
        if (! $element instanceof backup_nested_element) {
            throw new backup_step_exception('question_pmatchjme_answers_bad_parent_element',
                                                $element);
        }

        // Define the elements.
        $answers = new backup_nested_element('pmatchjme_answers');
        $answer = new backup_nested_element('pmatchjme_answer', ['id'],
            ['answerid', 'atomcount']);

        // Build the tree.
        $element->add_child($answers);
        $answers->add_child($answer);

        // Set the sources.
        $answer->set_source_sql('
                SELECT pa.*
                FROM {question_answers} ans,
                     {qtype_pmatchjme_answers} pa
                WHERE ans.question = :question AND ans.id = pa.answerid
                ORDER BY id',
                ['question' => backup::VAR_PARENTID]);
        // Don't need to annotate ids nor files.
    }
}
