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
 * This file contains format handler class, which the export format request.
 *
 * This file contains format handler class, which the export format request,
 * by instantiating the requested format class and returning an instance or that class.
 *
 * @package    block_export_quiz
 * @copyright  2019 onwards Ashish Pawar (github : CustomAP)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_export_quiz\export\dependencies;

defined('MOODLE_INTERNAL') || die();

use question_edit_contexts;
use moodle_exception;

/**
 * This class contains all format handler methods
 */
class format_handler
{
    /**
     * This method handles the file format request and returnes instance of the selected file format class.
     * @param object $courseinstance
     * @param string $format
     * @param array $questiondata
     * @return object $qformat
     */
    public function get_file_format($courseinstance, string $format, array $questiondata) {
        global $CFG, $COURSE;

        $contexts = new question_edit_contexts($courseinstance);
        // Check if the question format is readable, if yes import it.
        // This way support is added for any third-party question format installed.
        if (!is_readable($CFG->dirroot . "/question/format/{$format}/format.php")) {
            throw new moodle_exception(get_string('unknowformat', 'block_export_quiz'));
        } else {
            require_once($CFG->dirroot . "/question/format/{$format}/format.php");
        }

         // Set up the export format.
         $classname = 'qformat_' . $format;
         $qformat = new $classname();
         $qformat->setContexts($contexts->having_one_edit_tab_cap('export'));
         $qformat->setCourse($COURSE);
         $qformat->setCattofile(false);
         $qformat->setContexttofile(false);
         $qformat->setQuestions($questiondata);

         return $qformat;
    }


}