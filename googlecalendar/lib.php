<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_googlecalendar
 * @copyright   2022 Javier Mejia
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @param moodleform $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 * https://docs.moodle.org/dev/Callbacks
 * This function name depends on which plugin is implementing it. So if you were
 * implementing mod_wordsquare
 * This function would be called wordsquare_coursemodule_standard_elements
 * (the mod is assumed for course activities)
 */
function local_googlecalendar_coursemodule_standard_elements($formwrapper, $mform) {
    $elementname1 = 'checkbox';
    $elementname2 = 'fromdate';
    $elementname3 = 'todate';

    $modulename = $formwrapper->get_current()->modulename;
    if ($modulename == 'assign') {
        $mform->addElement('header', 'exampleheader', get_string('message1', 'local_googlecalendar'));
        $mform->addElement('advcheckbox', $elementname1, get_string('message1', 'local_googlecalendar'));
        $mform->setType($elementname1, PARAM_BOOL);
        $mform->setdefault($elementname1, ['checkbydefault']);

        $mform->addElement('date_selector', $elementname2, get_string('message2', 'local_googlecalendar'));
        $mform->setType($elementname2, PARAM_BOOL);
        $mform->setdefault($elementname2, time());

        $mform->addElement('date_selector', $elementname3, get_string('message3', 'local_googlecalendar'));
        $mform->setType($elementname3, PARAM_BOOL);
        $mform->setdefault($elementname3, time());
    }
}

/**
 * Process data from submitted form
 *
 * @param stdClass $data
 * @param stdClass $course
 * @return void
 * See plugin_extend_coursemodule_edit_post_actions in
 * https://github.com/moodle/moodle/blob/master/course/modlib.php
 */
function local_googlecalendar_coursemodule_edit_post_actions($data, $course) {
    // Pull apart $data and insert/update the database table.
    $callbacks = get_plugins_with_function('coursemodule_edit_post_actions', 'lib.php');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $data = $pluginfunction($data, $course);
        }
    }
    return $data;
}

/**
 * Validate the data in the new field when the form is submitted
 *
 * @param moodleform_mod $fromform
 * @param array $fields
 * @return void
 */
function local_googlecalendar_coursemodule_validation($fromform, $fields) {
    if (get_class($fromform) == 'mod_assign_mod_form') {
     \core\notification::add($fields['examplefield'], \core\notification::INFO);
    }
}