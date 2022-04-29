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
 * @licAAense     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


use local_googlecalendar\rest;

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
    GLOBAL $DB;
    $moduleid = $formwrapper->get_current()->coursemodule;
    $courseid = $formwrapper->get_current()->course;
    $modulename = $formwrapper->get_current()->modulename;
    
    $user = $DB->get_record_sql('SELECT checkbox FROM {googlecalendar} WHERE course = ? AND assign = ?;',[$courseid,$moduleid]);
    
    if ($modulename == 'assign') {
        $elementname1 = 'checkboxGoogleCalendar';
        $mform->addElement('header', 'exampleheader', get_string('message1', 'local_googlecalendar'));
        $mform->addElement('advcheckbox', $elementname1, get_string('message1', 'local_googlecalendar'));
        $mform->setType($elementname1, PARAM_BOOL);
        if(empty($user)){
            $mform->setdefault($elementname1, ['checkbydefault']);
        }
        else{
            if($user->checkbox == 1){
                $mform->setdefault($elementname1, 1);
            }
        }

    }
    
}

/**
 * Process data from submitted form
 *
 * @param stdClass $data
 * @param stdClass $course
 * @return $data
 */
function local_googlecalendar_coursemodule_edit_post_actions($data, $course) {
    GLOBAL $DB;
    

    $newobj = new stdClass();
    $newobj->course = $data->course;
    $newobj->assign = $data->coursemodule;
    $user = $DB->get_record_sql('SELECT id FROM {googlecalendar} WHERE course = ? AND assign = ?;',[$data->course,$data->coursemodule]);
    $newobj->checkbox = $data->checkboxGoogleCalendar;
    $context = context_course::instance($data->course);
    
    $datestart = new stdClass();
    $ddstart = strtotime($data->allowsubmissionsfromdate['day'] .'/'. $data->allowsubmissionsfromdate['month'] .'/'. $data->allowsubmissionsfromdate['year'] .' '. $data->allowsubmissionsfromdate['hour'] . ':'.$data->allowsubmissionsfromdate['minute'] . ':00');
    $datestart->dateTime = date('d/m/Y h:i:s',strtotime($data->allowsubmissionsfromdate['day'] .'/'. $data->allowsubmissionsfromdate['month'] .'/'. $data->allowsubmissionsfromdate['year'] .' '. $data->allowsubmissionsfromdate['hour'] . ':'.$data->allowsubmissionsfromdate['minute'] . ':00'));
    $datestartCheck = $data->allowsubmissionsfromdate['enabled'];


    $dateend = new stdClass();
    $dddend = strtotime($data->duedate['day'] .'/'. $data->duedate['month'] .'/'. $data->duedate['year'] .' '. $data->duedate['hour'] . ':'.$data->duedate['minute'] . ':00');
    $dateend->dateTime = date('d/m/Y h:i:s',strtotime($data->duedate['day'] .'/'. $data->duedate['month'] .'/'. $data->duedate['year'] .' '. $data->duedate['hour'] . ':'.$data->duedate['minute'] . ':00'));
    $dateendCheck = $data->duedate['enabled'];
    
    $dateend = new stdClass();
    if(empty($user)){  
        $DB->insert_record('googlecalendar',$newobj);
    }
    else{
        $newobj->id = $user->id;
        $DB->update_record('googlecalendar', $newobj);
    }

    if($data->checkboxGoogleCalendar == 1){
        //realizar la API
        $submissioncandidates = get_enrolled_users($context, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = '', $limitfrom = 0, $limitnum = 0);
        $attendees = [];
        foreach ($submissioncandidates as $d){
            $email = new stdClass();
            $email->email = $d->email;
            array_push($attendees,$email);
        }
        // Get an issuer from the id.
        $issuer = \core\oauth2\api::get_issuer(1);
        // Get an OAuth client from the issuer.
        $returnurl  = new moodle_url('/course/view.php');
        $returnurl->param('id',$data->course);
        $returnurl->param('sesskey',sesskey());
        $scopes = 'https://www.googleapis.com/auth/calendar';
        
        $client = \core\oauth2\api::get_user_oauth_client($issuer, $returnurl , $scopes);

        
        if (!$client->is_logged_in()) {
            redirect($client->get_login_url());
        }

        $service = new rest($client);
        $params = [
            'end.date' => $dateend->dateTime,
            'start.date' => $datestart->dateTime,
            'attendees' => $attendees,
            'calendarId' => "primary",
            'summary' => "Ejemplo"
        ];
        
        $pet = $service->call('create', $params);
        print_object($pet);
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