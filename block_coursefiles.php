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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/coursefiles/locallib.php');

class block_coursefiles extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_coursefiles');
    }

    function applicable_formats() {
        return array('course' => true);
    }

    function has_config() {
        return true;
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
        $filelist = block_coursefiles_get_filelist(5);

        // Get the sum total of file storage usage for the course.
        $sizetotal = block_coursefiles_get_total_filesize();

        if (!$filelist) {
            $this->content->text = get_string('nofilesoncourse', 'block_coursefiles');
            return $this->content;
        }

        $o = '';
        $o .= html_writer::start_tag('div', array('class'=>'list-group mb-1 m-b-1 px-1 p-x-1'));
        foreach ($filelist as $file) {
            $o .= html_writer::start_tag('li', array('class'=>'list-group-item file-info row px-0 p-x-0'));
            $o .= html_writer::start_tag('div', array('class'=>'col-8 col-sm-8 fileinfo'));
            // Print file name
            $o .= html_writer::tag('strong', $file->filename);
            // Print author
            $o .= html_writer::tag('small', get_string('author', 'block_coursefiles').': '.$file->author, array('class'=>'d-block'));
            // Print timestamp
            $o .= html_writer::tag('small', get_string('timecreated', 'block_coursefiles').': '.date('d/m/y', $file->timecreated), array('class'=>'d-block'));
            $o .= html_writer::end_tag('div');
            $o .= html_writer::start_tag('div', array('class'=>'col-4 col-sm-4 text-right text-sm-right pl-0 p-l-0'));
            // Print size
            $o .= html_writer::tag('div', display_size($file->filesize), array('class'=>'badge'));
            $o .= html_writer::end_tag('div');
            $o .= html_writer::end_tag('li');
        }
        $o .= html_writer::end_tag('div');

        $sizetotal = get_string('totalfilesize', 'block_coursefiles', display_size($sizetotal));
        $this->content->text .= $OUTPUT->heading($sizetotal, '5');

        $this->content->text .= $OUTPUT->heading(get_string('topfive', 'block_coursefiles'), '6');
        $this->content->text .= $o;

        $viewmoreurl = new moodle_url('/blocks/coursefiles/view.php', array('courseid' => $COURSE->id));
        $this->content->text .= html_writer::link($viewmoreurl, get_string('viewmore'), array('class' => 'btn btn-default'));

        return $this->content;
    }
}