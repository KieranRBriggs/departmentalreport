<?php

defined('MOODLE_INTERNAL') || die;

// Add a link to the reports admin settings menu.
$ADMIN->add('reports', new admin_externalpage('reportdepartments', get_string('pluginname', 'report_departments'), "$CFG->wwwroot/report/departments/index.php"));

// No report settings.
$settings = null;

/*
if ($ADMIN->fulltree) {
	$roles -> $DB->get_records_sql('SELECT id, name FROM mdl_role);
	foreach($roles as $r) {
		$roletypes[$r->id] = $r->name;
	}
    $menu = array('1'=>'Manager', '3'=>'teacher', '5'=>'student');
  

    $settings->add(new admin_setting_configselect('departmentreport/managerroleid',
                       get_string('chooseroleid', 'report_departments'),
                       get_string('descchooseroleid', 'report_departments'), 'defaultroleid', $menu));

}
*/