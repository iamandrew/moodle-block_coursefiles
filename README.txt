moodle-block_coursefiles
===========================
Moodle block which provides information on the files currently attached to a course.


Requirements
============
This plugin requires Moodle 2.6


Changes
=======
2015-02-05 - Initial version


Installation
============
Install the plugin like any other plugin to folder
/blocks/coursefiles

See http://docs.moodle.org/26/en/Installing_plugins for details on installing Moodle plugins


Placement
=========
block_coursefiles is used ideally as sticky block and appears on all of your moodle pages at the same position

See http://docs.moodle.org/26/en/Block_settings#Making_a_block_sticky_throughout_the_whole_site for details about sticky blocks


Usage
=====
The block_coursefiles plugin has two views:
The main block view will show an overview of the file storage used as well as the largest 5 files on a course
The block has a report page that will show a list of all the files on a course, ordered by size.


Themes
======
block_coursefiles should work with all themes from moodle core.


Settings
========
block_coursefiles has neither a settings page nor settings in config.php.


Further information
===================
block_coursefiles is found in the Moodle Plugins repository: http://moodle.org/plugins/view.php?plugin=block_coursefiles

Report a bug or suggest an improvement: https://github.com/iamandrew/moodle-block_coursefiles/issues


Right-to-left support
=====================
This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send me a pull request on
github with modifications.


Copyright
=========
Andrew Davidson
