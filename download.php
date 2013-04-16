<?php
/**
* script for downloading of user lists
*/

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/report/departments/lib.php');

require_login();
require_capability('moodle/user:update', context_system::instance());

$return = $CFG->wwwroot.'/'.$CFG->dirroot.'/report/departments.php';

$format 		= optional_param('format', '', PARAM_ALPHA);
$hod			= optional_param('hod', 0, PARAM_INT); // Hod id number
$timefrom   	= optional_param('date', 0, PARAM_INT); // how far back to look...
$showteachers   = optional_param('showteachers', false, PARAM_BOOL); // Show teachers in results or not

$params = array();

$params['hod'] = $hod;
$params['date'] = $timefrom;
$params['showteachers'] = $showteachers;



if ($format) {
    $fields = array('coursename'        => 'Course Name',
                    'createdon'  		=> 'Created On',
                    'enrolledstudents'	=> 'Enrolled Students',
                    'logins' 			=> 'Logins',
                    'lastlogin'  		=> 'Last Login',
                    'lastupdate'  		=> 'Last Update',
                    'resources' 		=> 'Resources',
                    'activites' 		=> 'Activites');


    switch ($format) {
        case 'csv' : user_download_csv($fields, $params);
        case 'ods' : user_download_ods($fields);
        case 'xls' : user_download_xls($fields);

    }
    die;
}


function user_download_ods($fields) {
    global $CFG, $SESSION, $DB;

    require_once("$CFG->libdir/odslib.class.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');

    $filename = clean_filename(get_string('users').'.ods');

    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();

    $worksheet[0] = $workbook->add_worksheet('');
    $col = 0;
    foreach ($fields as $fieldname) {
        $worksheet[0]->write(0, $col, $fieldname);
        $col++;
    }

    $row = 1;
    foreach ($SESSION->bulk_users as $userid) {
        if (!$user = $DB->get_record('user', array('id'=>$userid))) {
            continue;
        }
        $col = 0;
        profile_load_data($user);
        foreach ($fields as $field=>$unused) {
            $worksheet[0]->write($row, $col, $user->$field);
            $col++;
        }
        $row++;
    }

    $workbook->close();
    die;
}

function user_download_xls($fields) {
    global $CFG, $SESSION, $DB;

    require_once("$CFG->libdir/excellib.class.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');

    $filename = clean_filename(get_string('users').'.xls');

    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();

    $worksheet[0] = $workbook->add_worksheet('');
    $col = 0;
    foreach ($fields as $fieldname) {
        $worksheet[0]->write(0, $col, $fieldname);
        $col++;
    }

    $row = 1;
    foreach ($SESSION->bulk_users as $userid) {
        if (!$user = $DB->get_record('user', array('id'=>$userid))) {
            continue;
        }
        $col = 0;
        profile_load_data($user);
        foreach ($fields as $field=>$unused) {
            $worksheet[0]->write($row, $col, $user->$field);
            $col++;
        }
        $row++;
    }

    $workbook->close();
    die;
}

function user_download_csv($fields, $params) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/csvlib.class.php');
    $data = get_download_data($params);

    $filename = clean_filename(get_string('filename','report_departments')).'_user'.$params['hod'];

    $csvexport = new csv_export_writer();
    $csvexport->set_filename($filename);
    $csvexport->add_data($fields);


    $csvexport->download_file();
    die;
}
