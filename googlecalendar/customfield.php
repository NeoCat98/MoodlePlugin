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
 * Manage course custom fields
 *
 * @package local_googlecalendar
 * @copyright 2022 Javier Mejia
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
global $COURSE;
$output = $PAGE->get_renderer('core_customfield');
global $SESSION;
       
$issuer = \core\oauth2\api::get_issuer(1);
$returnurl  = new moodle_url('/local/googlecalendar/customfield.php');
$returnurl->param('sesskey',sesskey());
$scopes = 'https://www.googleapis.com/auth/calendar';  
$datestart = new stdClass();
$dateend = new stdClass(); 
$attendee = new stdClass(); 
$attendees = [];
$attendee->email = '00184217@uca.edu.sv';
$attendee2->email = '00004817@uca.edu.sv';
$summary = 'Peche es feo';
array_push($attendees,$attendee);
array_push($attendees,$attendee2);
$dateend->dateTime = '2022-04-30T17:06:02.000Z';
$datestart->dateTime = '2022-04-29T17:06:02.000Z';
$client = \core\oauth2\api::get_user_oauth_client($issuer, $returnurl , $scopes);
if (!$client->is_logged_in()) {
    redirect($client->get_login_url());
    
}else{
    //crear un nuevo calendario
    $service = new \local_googlecalendar\rest($client);
    $params = [
        'end' => $dateend,
        'summary' => $summary,
        'start' => $datestart,
        'attendees' => $attendees
    ];      
    $SESSION->myvar = $params;
    $response = $service->call('insert',[] ,json_encode($SESSION->myvar));

}


echo $output->header(),
     $output->heading($response),
     $output->footer();