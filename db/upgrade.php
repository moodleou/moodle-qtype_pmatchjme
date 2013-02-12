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
 * Pmatch with jme editor question type upgrade code.
 *
 * @package   qtype_pmatchjme
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the pmatchjme question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_pmatchjme_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012062300) {
        $toupdate = $DB->get_records_menu("question", array('qtype' => 'pmatchjme'), '', 'id, 0');

        if ($toupdate) {
            list($qidssql, $qids) = $DB->get_in_or_equal(array_keys($toupdate));
            $DB->execute("UPDATE {qtype_pmatch} SET allowsubscript = 0, allowsuperscript = 1 WHERE questionid $qidssql",
                                                                                                                $qids);
        }
        // Pmatchjme savepoint reached.
        upgrade_plugin_savepoint(true, 2012062300, 'qtype', 'pmatchjme');
    }

    if ($oldversion < 2012062500) {

        // Define key answerid_fk (foreign-unique) to be dropped form qtype_pmatchjme_answers.
        $table = new xmldb_table('qtype_pmatchjme_answers');
        $key = new xmldb_key('answerid_fk', XMLDB_KEY_FOREIGN_UNIQUE, array('answerid'), 'question_answers', array('id'));

        // Launch drop key answerid_fk.
        $dbman->drop_key($table, $key);

        // Next pmatchjme savepoint reached.
        upgrade_plugin_savepoint(true, 2012062500, 'qtype', 'pmatchjme');
    }

    if ($oldversion < 2012062501) {

        // Changing precision of field answerid on table qtype_pmatchjme_answers to (10).
        $table = new xmldb_table('qtype_pmatchjme_answers');
        $field = new xmldb_field('answerid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'atomcount');

        // Launch change of precision for field answerid.
        $dbman->change_field_precision($table, $field);

        // Next pmatchjme savepoint reached.
        upgrade_plugin_savepoint(true, 2012062501, 'qtype', 'pmatchjme');
    }

    if ($oldversion < 2012062502) {

        // Define key answerid_fk (foreign-unique) to be added to qtype_pmatchjme_answers.
        $table = new xmldb_table('qtype_pmatchjme_answers');
        $key = new xmldb_key('answerid_fk', XMLDB_KEY_FOREIGN_UNIQUE, array('answerid'), 'question_answers', array('id'));

        // Launch add key answerid_fk.
        $dbman->add_key($table, $key);

        // Next pmatchjme savepoint reached.
        upgrade_plugin_savepoint(true, 2012062502, 'qtype', 'pmatchjme');
    }

    return true;
}

