<?php

defined('MOODLE_INTERNAL') || die;

// Add a link to the reports admin settings menu.
$ADMIN->add('reports', new admin_externalpage('reportdepartments', get_string('pluginname', 'report_departments'), "$CFG->wwwroot/report/departments/index.php"));

// No report settings.
$settings = null;