# Export-Quiz
A plugin for exporting quiz in moodle.

This plugin is a block plugin which displays a list of quizes in a particular course. Click on the quiz you want to download, and the plugin does the rest.

This plugin exports a quiz in any available installed format.

## Minimum Moodle Requirments
Due to the major code refactoring that took place with release 2.0, the minimum system reequipments to install this version is:

- PHP >= 7.0
- Moodle >= 3.0.10 (Build: 20170508 | Version  = 2015111610.00)

For old moodle versions, please download export quiz version 1.0.2


## Release notes for version 2.0 
* Plugin code base have been refactored which will open the door in future for easy bug fixing and new feature implementation.
* Small update for form labels. 
  * The new change will help the labels get displayed probably on smaller screens.
* Improved role capabilities. 
  * In the previous release, once a new block is added to course, it is by default visible to all students, 
  this could possibly cause issues, if the teacher did not intend to have the student export the quiz content.
  * With this release, a new capability has been added to the Block, which by default will block students from having access to view the block, 
  Unless Moodle administrator, Managers or Teachers “If have access to” they can update the user permeation on the student’s role to change the ability to view the plugin from “Prevent to Allow”.
This action can be done from Moodle administration section or from within selected course. 

License
-------
Licensed under the [GNU GPL v3 or later License](https://www.gnu.org/licenses/gpl-3.0.en.html).