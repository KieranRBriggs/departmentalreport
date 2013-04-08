<?php

function get_hods() {
	global $CFG, $DB;
	
	// Change number for each version of moodle to show the hod role id.
	$hod = 1;
	
	$sql = "SELECT DISTINCT usr.id, CONCAT_WS(' ',firstname,lastname) AS hod
			FROM mdl_user AS usr
			JOIN mdl_role_assignments AS ra ON ra.userid = usr.id
			WHERE ra.roleid =".$hod.'
			ORDER BY usr.lastname';
	
	$hods = $DB->get_records_sql($sql);
	foreach($hods as $h) {
		$content[$h->id] = $h->hod;
	}
	return $content;
}

function get_time($param) {
	global $CFG, $DB;
	
	$timeoptions = array();
	// get minimum log time for this course
	$minlog = $DB->get_field_sql('SELECT min(time) FROM {log}');//S WHERE course = ?', array($course->id));
	
	$now = usergetmidnight(time());
	
	// days
	for ($i = 1; $i < 7; $i++) {
	    if (strtotime('-'.$i.' days',$now) >= $minlog) {
	        $timeoptions[strtotime('-'.$i.' days',$now)] = get_string('numdays','moodle',$i);
	    }
	}
	// weeks
	for ($i = 1; $i < 10; $i++) {
	    if (strtotime('-'.$i.' weeks',$now) >= $minlog) {
	        $timeoptions[strtotime('-'.$i.' weeks',$now)] = get_string('numweeks','moodle',$i);
	    }
	}
	// months
	for ($i = 2; $i < 12; $i++) {
	    if (strtotime('-'.$i.' months',$now) >= $minlog) {
	        $timeoptions[strtotime('-'.$i.' months',$now)] = get_string('nummonths','moodle',$i);
	    }
	}
	// try a year
	if (strtotime('-1 year',$now) >= $minlog) {
	    $timeoptions[strtotime('-1 year',$now)] = get_string('lastyear');
	}
	
	return $timeoptions;
}

function get_data($params) {
	
	global $CFG, $DB;
	
	$managerrole = 1;
	$managerrole = get_config('departmentreport', 'managerroleid');
		
	$coursessql = 'SELECT fullname AS course, COUNT(course.id) AS Students, course.id AS cid, course.timecreated AS created
			FROM mdl_role_assignments AS asg
			JOIN mdl_context AS context ON asg.contextid = context.id AND context.contextlevel = 50
			JOIN mdl_user AS usr on usr.id = asg.userid
			JOIN mdl_course AS course ON context.instanceid = course.id
			WHERE asg.roleid = ' .$managerrole. ' AND usr.id = '.$params['hod'].'
			GROUP BY course.id
			ORDER BY COUNT(course.id) DESC';
	
	$courses = $DB->get_records_sql($coursessql);


	$table = new html_table();
	$table->align = array('left', 'center', 'center', 'center', 'center', 'center', 'center', 'center');
	$table->head = array('Course Name', 'Created On', 'Enroled Students', 'Logins', 'Last Login', 'Last Update', 'Resources', 'Activites');
	
	foreach ($courses as $c) {
		$resourcesql = 'SELECT count(id) AS res FROM mdl_resource WHERE course = '. $c->cid;
		$resource = $DB->get_record_sql($resourcesql);
		$modulesql = 'SELECT count(*) AS mods FROM mdl_course_modules AS cm WHERE cm.course ='. $c->cid .' AND module <> 17';
		$module = $DB->get_record_sql($modulesql);
		$viewsql = 'SELECT DISTINCT count(id) AS views, MAX(time) AS lastlogin FROM mdl_log WHERE course = '. $c->cid . ' AND action = "view" AND time > '.$params["date"];
		$view = $DB->get_record_sql($viewsql);
		
		$updatesql = 'SELECT course, MAX(time) AS Updated FROM mdl_log
						WHERE (action LIKE "%add%" OR action = "update") AND course = '.$c->cid;
		$update = $DB->get_record_sql($updatesql);
		$row = array();
		$row[] = '<a href="'.$CFG->wwwroot.'/report/outline/index.php?id='.$c->cid.'">'.$c->course.'</a>';
		$row[] = userdate($c->created, get_string('strftimedatefullshort', 'langconfig'));
		$row[] = '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$c->cid.'">'.$c->students.'</a>';
		$row[] = $view->views;	
		$row[] = userdate($view->lastlogin,get_string('strftimedatetimeshort','langconfig'));
		$row[] = userdate($update->updated,get_string('strftimedatetimeshort','langconfig'));	
		$row[] = $resource->res;	
		$row[] = $module->mods;	
		
		$table->data[] = $row;
	}
	
	$content = html_writer::table($table);
	
	return $content;
}

function get_never_viewed_courses($params) {
	
	global $CFG, $DB;
	
	$sql = 'SELECT id, fullname AS Course
,(SELECT Count( ra.userid ) AS Users FROM mdl_role_assignments AS ra
JOIN mdl_context AS ctx ON ra.contextid = ctx.id
WHERE ra.roleid = 3 AND ctx.instanceid = c.id) AS Teachers
FROM mdl_course AS c
ORDER BY Teachers ASC';

	$courses = $DB->get_records_sql($sql);
	
	$table = new html_table();
	$table->align = array('left', 'left');
	$table->head = array('Course Name', 'No. of Resources');
	foreach ($courses as $c) {
		
		$resourcesql = 'SELECT count(id) AS res FROM mdl_resource WHERE course = '. $c->id;
		$resource = $DB->get_records_sql($resourcesql);
		$row = array();
		$row[] = $c->course;
		foreach($resource as $r) {
			$row[] = $r->res;	
		}
		//$row[] = $c->students;
		
		$table->data[] = $row;
	}
	
	$content = html_writer::table($table);
	
	return $content;

}