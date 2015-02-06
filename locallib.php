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
 * Local lib functions
 *
 * @package    block_coursefiles
 * @copyright  2014 Andrew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function block_coursefiles_get_filelist($limit=0) {
    global $COURSE, $DB;

    $context = context_course::instance($COURSE->id);
    $contextcheck = $context->path . '/%';

    // Get the top file files used on the course by size.
    $sql = "SELECT f.*
            FROM {files} f
            JOIN {context} ctx ON f.contextid = ctx.id
            WHERE ".$DB->sql_concat('ctx.path',"'/'")." LIKE ?
            AND f.filename != '.'
            ORDER BY f.filesize DESC";
    $params = array($contextcheck);
    $filelist = $DB->get_records_sql($sql, $params, 0, $limit);

    return $filelist;
}

function block_coursefiles_get_total_filesize() {
    global $COURSE, $DB;

    $context = context_course::instance($COURSE->id);
    $contextcheck = $context->path . '/%';

    $sql = "SELECT SUM(f.filesize)
                FROM {files} f
                JOIN {context} ctx ON f.contextid = ctx.id
                WHERE ".$DB->sql_concat('ctx.path',"'/'")." LIKE ?
                AND f.filename != '.'";
    $params = array($contextcheck);
    $sizetotal = $DB->get_field_sql($sql, $params);

    return $sizetotal;
}

function block_coursefiles_get_all_courses() {
    global $DB;

    $sql = "SELECT courselist.id AS courseid, courselist.fullname AS name, SUM(courselist.filesize) AS filesize
            FROM (

                SELECT c.id, c.fullname, cx.contextlevel,f.component, f.filearea, f.filename, f.filesize
                FROM {context} cx
                JOIN {course} c ON cx.instanceid=c.id
                JOIN {files} f ON cx.id=f.contextid
                WHERE f.filename <> '.'
                AND f.component NOT IN ('private','draft')

                UNION

                SELECT cm.course, c.fullname, cx.contextlevel,f.component, f.filearea, f.filename, f.filesize
                FROM {files} f
                JOIN {context} cx ON f.contextid = cx.id
                JOIN {course_modules} cm ON cx.instanceid=cm.id
                JOIN {course} c ON cm.course=c.id
                WHERE filename <> '.'

                UNION

                SELECT c.id, c.fullname, cx.contextlevel, f.component, f.filearea, f.filename, f.filesize
                from {block_instances} bi
                join {context} cx on (cx.contextlevel=80 and bi.id = cx.instanceid)
                join {files} f on (cx.id = f.contextid)
                join {context} pcx on (bi.parentcontextid = pcx.id)
                join {course} c on (pcx.instanceid = c.id)
                where filename <> '.'

            ) AS courselist GROUP BY courseid ORDER BY filesize DESC";
    $courselist = $DB->get_records_sql($sql);

    return $courselist;
}