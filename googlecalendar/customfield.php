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
$client = \core\oauth2\api::get_user_oauth_client($issuer, $returnurl , $scopes);
if (!$client->is_logged_in()) {
    redirect($client->get_login_url());
    
}else{
    //crear un nuevo calendario
    $service = new \local_googlecalendar\rest($client);
    $SESSION->summary = " ";
    $params = ['summary' => $SESSION->summary];       
    $SESSION->myvar = $params;
    $response = $service->call('create', $SESSION->myvar);
    print_object($response);

}


echo $output->header(),
     $output->heading($response),
     $output->footer();