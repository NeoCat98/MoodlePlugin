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

    $modulename = $formwrapper->get_current()->modulename;
    

    if ($modulename == 'assign') {
        $handler = local_googlecalendar\customfield\mod_handler::create();
        $handler->set_parent_context($formwrapper->get_context()); // For course handler only.
        $cm = $formwrapper->get_coursemodule();
        if (empty($cm)) {
            $cmid = 0;
        } else {
            $cmid = $cm->id;
        }
        $handler->instance_form_definition($mform, $cmid);
        // Prepare custom fields data.
        $data = $formwrapper->get_current();
        $oldid = $data->id;
        $data->id = $cmid;
        $handler->instance_form_before_set_data($data);
        $data->id = $oldid;
    }

    /**
    *
    *$elementname1 = 'checkboxGoogle';
    *    $elementname2 = 'fromdate';
    *    $elementname3 = 'todate';
    *
    *    
    *    $mform->addElement('header', 'exampleheader', get_string('message1', 'local_googlecalendar'));
    *    $mform->addElement('advcheckbox', $elementname1, get_string('message1', 'local_googlecalendar'));
    *    $mform->setType($elementname1, PARAM_BOOL);
    *    $mform->setdefault($elementname2, time());
*
 *       $mform->addElement('date_time_selector', $elementname2, get_string('message2', 'local_googlecalendar'));
  *      $mform->setType($elementname2, PARAM_INT);
   *     $mform->setdefault($elementname2, time());
*
 *       $mform->addElement('date_time_selector', $elementname3, get_string('message3', 'local_googlecalendar'));
  *      $mform->setType($elementname3, PARAM_INT);
   *     $mform->setdefault($elementname3, time());
         */
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
    $handler = local_googlecalendar\customfield\mod_handler::create();
    $handler->set_parent_context(context_course::instance($course->id));
    $data->id = $data->coursemodule;
    $handler->instance_form_save($data, true);
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
    $handler = local_modcustomfields\customfield\mod_handler::create();
    $handler->set_parent_context(context_course::instance($fields['course']));
    return $handler->instance_form_validation($fields, []);
}