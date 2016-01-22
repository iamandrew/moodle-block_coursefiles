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
 * @copyright 2016 Andrew Davidson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/blocks/coursefiles/locallib.php');

require_login();
$courseid = required_param('courseid', PARAM_INT); // If no courseid is given.
$course = $DB->get_record('course', array('id' => $courseid));

$context = context_course::instance($courseid);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/coursefiles/view.php', array('courseid' => $courseid));
$PAGE->set_title($course->fullname.' '.get_string('files'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('course');
$PAGE->navbar->add(get_string('pluginname', 'block_coursefiles'));

require_capability('block/coursefiles:viewlist', $context);

$filelist = block_coursefiles_get_filelist();
$sizetotal = block_coursefiles_get_total_filesize();

$table = new html_table();
$table->attributes = array('style' => 'font-size: 80%;');
$table->head = array(
    get_string('name'),
    get_string('author', 'block_coursefiles'),
    get_string('timecreated', 'block_coursefiles'),
    get_string('filesize', 'block_coursefiles')
);

foreach ($filelist as $file) {
    $row = new html_table_row();
    $row->cells[] = $file->filename;
    $row->cells[] = $file->author;
    $row->cells[] = date('d/m/y', $file->timecreated);
    $row->cells[] = display_size($file->filesize);
    $table->data[] = $row;
}


echo $OUTPUT->header();
echo $OUTPUT->heading($course->fullname);
echo $OUTPUT->heading(get_string('totalfilesize', 'block_coursefiles', display_size($sizetotal)), 3, 'main');

echo html_writer::table($table);

echo $OUTPUT->footer();
