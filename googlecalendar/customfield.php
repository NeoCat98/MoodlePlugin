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
use local_googlecalendar\rest;
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
global $COURSE;
$output = $PAGE->get_renderer('core_customfield');

$attendees = [
    'email' => '00004817@uca.edu.sv',
    'email' => '00076017@uca.edu.sv'
];
$dateend->dateTime = date('d/m/Y h:i:s',strtotime('2022-04-28T09:00:00-07:00'));
$datestart->dateTime = date('d/m/Y h:i:s',strtotime('2022-04-27T09:00:00-07:00'));
       
$issuer = \core\oauth2\api::get_issuer(1);
$returnurl  = new moodle_url('/local/googlecalendar/customfield.php');
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
    'attendees' => array(
        
    ),
    'summary' => "Ejemplo"
];
$pet = $service->call('create', $params);
$context = context_course::instance($COURSE->id);

echo $output->header(),
     $output->heading($pet),
     $output->footer();