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
 * Block to show course files and usage
 *
 * @package   block_coursefiles
 * @copyright 2014 Andrew Davidson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/blocks/coursefiles/locallib.php');

require_login();
$pluginstr = get_string('pluginname', 'block_coursefiles');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/coursefiles/all.php');
$PAGE->set_title($pluginstr);
$PAGE->set_heading($pluginstr);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add($pluginstr);

require_capability('block/coursefiles:viewlist', $context);

$courselist = block_coursefiles_get_all_courses();

$table = new html_table();
$table->attributes = array('style' => 'font-size: 80%;');
$table->head = array(
    get_string('name'),
    get_string('totalsize', 'block_coursefiles')
);

$totalsize = 0;

foreach ($courselist as $course) {
    $row = new html_table_row();
    $courselink = new moodle_url('/course/view.php', array('id' => $course->courseid));
    $coursefileslink = new moodle_url('/blocks/coursefiles/view.php', array('courseid' => $course->courseid));
    $row->cells[] = html_writer::link($courselink, $course->name)
                        .' ('.html_writer::link($coursefileslink, get_string('viewcoursefiles', 'block_coursefiles')).')';
    $row->cells[] = display_size($course->filesize);
    $table->data[] = $row;
    $totalsize += $course->filesize;
}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('totalfilesize', 'block_coursefiles', display_size($totalsize)), 3, 'main');

echo html_writer::table($table);

echo $OUTPUT->footer();
