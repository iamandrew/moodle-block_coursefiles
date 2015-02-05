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

defined('MOODLE_INTERNAL') || die();

class block_coursefiles extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_coursefiles');
    }

    function applicable_formats() {
        return array('course' => true);
    }

    function has_config() {
        return false;
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG, $DB, $OUTPUT, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (!has_capability('block/coursefiles:viewlist', context_course::instance($COURSE->id))) {
            return $this->content;
        }

        $context = context_course::instance($COURSE->id);

        $contextcheck = $context->path . '/%';

        // Get the top file files used on the course by size.
        $sql = "SELECT f.*
                FROM mdl_files f
                JOIN mdl_context ctx ON f.contextid = ctx.id
                WHERE CONCAT(ctx.path, '/') LIKE '$contextcheck'
                AND f.filename != '.'
                ORDER BY f.filesize DESC
                LIMIT 5";
        $filelist = $DB->get_records_sql($sql);

        // Get the sum total of file storage usage for the course.
        $sql = "SELECT SUM(f.filesize)
                FROM mdl_files f
                JOIN mdl_context ctx ON f.contextid = ctx.id
                WHERE CONCAT(ctx.path, '/') LIKE '$contextcheck'
                AND f.filename != '.'";
        $sizetotal = $DB->get_field_sql($sql);

        if (!$filelist) {
            $this->content->text = get_string('nofilesoncourse', 'block_coursefiles');
            return $this->content;
        }

        $table = new html_table();
        $table->attributes = array('style' => 'font-size: 80%;', 'class' => '');
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

        $sizetotal = get_string('totalfilesize', 'block_coursefiles', display_size($sizetotal));
        $this->content->text .= $OUTPUT->heading($sizetotal, '5');

        $this->content->text .= $OUTPUT->heading(get_string('topfive', 'block_coursefiles'), '6');
        $this->content->text .= html_writer::table($table);

        $viewmoreurl = new moodle_url('/blocks/coursefiles/view.php', array('courseid' => $COURSE->id));
        $this->content->text .= html_writer::link($viewmoreurl, get_string('viewmore'));

        return $this->content;
    }
}