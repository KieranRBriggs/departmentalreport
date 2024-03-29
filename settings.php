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
 * Departmental Report.
 *
 * Creates the setting page for the report so an admin can set the role type for the report to run against.
 *
 * @package report_departments
 * @copyright 2013 Kieran Briggs - The Sheffield College
 * @email: kieran.briggs@sheffcol.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Add a link to the reports admin settings menu.
$ADMIN->add('reports', new admin_externalpage('reportdepartments', get_string('pluginname', 'report_departments'), "$CFG->wwwroot/report/departments/index.php"));


if ($ADMIN->fulltree) {
	global $DB;
	$roles = $DB->get_records_sql('SELECT id, name FROM mdl_role');
	foreach($roles as $r) {
		$roletypes[$r->id] = $r->name;
	}
    
    
  

    $settings->add(new admin_setting_configselect('departmentreport/managerroleid',
                       get_string('chooseroleid', 'report_departments'),
                       get_string('descchooseroleid', 'report_departments'), 'defaultroleid', $roletypes));

}
