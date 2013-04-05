<?php 

// This report shows a list of all courses and what activity is happening for a department head.

require('../../config.php');
require_once($CFG->dirroot.'/lib/statslib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/report/departments/lib.php');

$hod	= optional_param('hod', 0, PARAM_INT); // Hod id number
$timefrom   = optional_param('timefrom', 0, PARAM_INT); // how far back to look...

$params = array();


$params['hod'] = $hod;
$params['date'] = $timefrom;


$PAGE->set_url('/report/departments/index.php', $params);
$PAGE->set_pagelayout('report');

admin_externalpage_setup('reportdepartments', '', null, '', array('pagelayout'=>'report'));

$hods = get_hods();
$timeoptions = get_time();
echo $OUTPUT->header();

echo '<div id="options"><form class="settingsform" action="'.$CFG->wwwroot.'/report/departments/index.php" method="get">';
echo '<label for="menuhod">Department Head: </label>'."\n";
echo html_writer::select($hods, "hod", $hod);
echo '  |  <label for="menutimefrom">Show logins for last: </label>'."\n";
echo html_writer::select($timeoptions,'timefrom',$timefrom);
echo '<span style="float:right;"><input type="submit" value="Run Report" /></span></form></div>';

//switch ($reporttype) {
//	case showhtml:
		$results = get_data($params);
		echo $results;
		echo '<p><em>Click on the course title for a more indepth report on that course.</em></p>';
		
//		break;
//	case excel:
//		break;
//}


echo $OUTPUT->footer();