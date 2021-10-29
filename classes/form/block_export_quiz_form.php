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
 * This file contains the Export Quiz Block.
 *
 * @package    block_export_quiz
 * @copyright  2019 onwards Ashish Pawar (github : CustomAP)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_export_quiz\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/questionlib.php');

use moodleform;
use questionlib;

/**
 * Form to export questions from the quiz.
 *
 * @copyright  2019 onwards Ashish Pawar (github : CustomAP)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_export_quiz_form extends moodleform
{
    /**
     * moodle form API overrided definition() method,
     * and it contains the required form elements to render the form to the users
     */
    public function definition() {
        $mform = $this->_form;

        $quizes = $this->_customdata['quiz'];
        $format = get_import_export_formats('export');

        $formats = array();

        foreach ($format as $shortname => $fileformatname) {
            $formats[$shortname] = $fileformatname;
        }

        $mform->addElement('html', '<div>');
        $mform->addElement('html', ' <label for="exampleInputEmail1">'.get_string('quiz', 'block_export_quiz').'</label>');
        // Quiz select.
        $mform->addElement('select', 'quiz', '', $quizes);
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div>');
        $mform->addElement('html', ' <label for="exampleInputEmail1">'.get_string('format', 'block_export_quiz').'</label>');
        // Format select.
        $mform->addElement('select', 'format', '', $formats);
        $mform->addElement('html', '</div>');
        // Submit buttons.
        $this->add_action_buttons(false, get_string('export', 'block_export_quiz'));
    }
}
