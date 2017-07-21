<?php
/*
 * Copyright (C) 2016 - 2017 Nico Alt
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * See the file "LICENSE.md" for the full license governing this code.
 */
namespace LegionBoard\Resources;

require_once __DIR__ . '/AbstractResource.php';

class Changes extends AbstractResource
{

    /**
     * Connect with the database.
     */
    public function __construct($user)
    {
        parent::__construct($user);
        $this->resource = 'changes';
    }

    /**
     * Get one or more changes.
     */
    public function get(
        $id = null,
        $teachers = null,
        $courses = null,
        $coveringTeacher = null,
        $startBy = null,
        $endBy = null,
        $seeReasons = false,
        $seePrivateTexts = false,
        $seeTimes = false,
        $subjects = null
    ) {
        $changes = array();
        // Filter by teachers
        if (!empty($teachers)) {
            $filteredTeachers = array_filter($teachers, function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            if (!empty($filteredTeachers)) {
                sort($teachers);
                foreach ($teachers as $teacher) {
                    // Add where clause for teacher
                    $teacher = $this->database->escape_string($teacher);
                    $sqlTeachers .= (empty($sqlTeachers) ? "" : " OR ") . "teacher LIKE '$teacher'";
                }
            }
        }
        // Filter by courses
        if (!empty($courses)) {
            $filteredCourses = array_filter($courses, function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            if (!empty($filteredCourses)) {
                sort($courses);
                foreach ($courses as $course) {
                    // Add where clause for course
                    $course = $this->database->escape_string($course);
                    $sqlCourses .= (empty($sqlCourses) ? "" : " OR ") . "course LIKE '$course'";
                }
            }
        }
        // Filter by subjects
        if (!empty($subjects)) {
            $filteredSubjects = array_filter($subjects, function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            if (!empty($filteredSubjects)) {
                sort($subjects);
                foreach ($subjects as $subject) {
                    // Add where clause for subject
                    $subject = $this->database->escape_string($subject);
                    $sqlSubjects .= (empty($sqlSubjects) ? "" : " OR ") . "subject LIKE '$subject'";
                }
            }
        }
        // Filter by ID
        if (isset($id)) {
            $id = $this->database->escape_string($id);
            $sqlID = " id LIKE '$id'";
        }
        // Filter by covering teacher
        if (isset($coveringTeacher)) {
            $coveringTeacher = $this->database->escape_string($coveringTeacher);
            $sqlCoveringTeacher = " coveringTeacher LIKE '$coveringTeacher'";
        }
        // Build SELECT
        $sql = "SELECT * FROM " . \LegionBoard\Database::$tableChanges;
        if (!empty($sqlTeachers) || !empty($sqlCourses)  || !empty($sqlSubjects) || !empty($sqlID) || !empty($sqlCoveringTeacher)) {
            $sql .= " WHERE";
            if (!empty($sqlTeachers)) {
                $sql .= " (" . $sqlTeachers . ")";
            }
            if (!empty($sqlCourses)) {
                if (!empty($sqlTeachers)) {
                    $sql .= " AND";
                }
                $sql .= " (" . $sqlCourses . ")";
            }
            if (!empty($sqlSubjects)) {
                if (!empty($sqlTeachers) || !empty($sqlCourses)) {
                    $sql .= " AND";
                }
                $sql .= " (" . $sqlSubjects . ")";
            }
            if ((!empty($sqlTeachers) || !empty($sqlCourses) || !empty($sqlSubjects)) && !empty($sqlID)) {
                $sql .= " AND";
            }
            $sql .= $sqlID;
            if ((!empty($sqlTeachers) || !empty($sqlCourses) || !empty($sqlSubjects) || !empty($sqlID)) && !empty($sqlCoveringTeacher)) {
                $sql .= " AND";
            }
            $sql .= $sqlCoveringTeacher;
        }
        $query = $this->database->query($sql);
        if ($query && $query->num_rows != 0) {
            while ($column = $query->fetch_array()) {
                // Filter out if end of change is before given start
                if ($startBy != null) {
                    $dtColumnEnd = new \DateTime(substr($column['endingDate'], 0, 10));
                    $dtStart = new \DateTime($startBy);
                    if ($dtColumnEnd < $dtStart) {
                        continue;
                    }
                }
                // Filter out if start of change is after given end
                if ($endBy != null) {
                    $dtColumnStart = new \DateTime(substr($column['startingDate'], 0, 10));
                    $dtEnd = new \DateTime($endBy);
                    if ($dtColumnStart > $dtEnd) {
                        continue;
                    }
                }
                $change = array(
                            'id' => $column['id'],
                            'startingDate' => $column['startingDate'],
                            'startingHour' => $column['startingHour'],
                            'endingDate' => $column['endingDate'],
                            'endingHour' => $column['endingHour'],
                            'type' => $column['type'],
                            'course' => $column['course'],
                            'teacher' => $column['teacher'],
                            'coveringTeacher' => $column['coveringTeacher'],
                            'text' => $column['text'],
                            'subject' => $column['subject'],
                            'reason' => $seeReasons ? $column['reason'] : '-',
                            'privateText' => $seePrivateTexts ? $column['privateText'] : '-',
                            'added' => $seeTimes ? $column['added'] : '-',
                            'edited' => $seeTimes ? $column['edited'] : '-'
                            );
                $changes[] = $change;
            }
        }
        $filteredChanges = array_filter($changes);
        if (empty($filteredChanges)) {
            return null;
        }
        return $changes;
    }

    /**
     * Create a change.
     */
    public function create(
        $teacher,
        $course,
        $coveringTeacher,
        $startingDate,
        $startingHour,
        $endingDate,
        $endingHour,
        $type,
        $text,
        $reason,
        $privateText
    ) {
        // Escape strings
        $teacher = $this->database->escape_string($teacher);
        $course = $this->database->escape_string($course);
        $coveringTeacher = $this->database->escape_string($coveringTeacher);
        $startingDate = $this->database->escape_string($startingDate);
        $startingHour = $this->database->escape_string($startingHour);
        $endingDate = $this->database->escape_string($endingDate);
        $endingHour = $this->database->escape_string($endingHour);
        $type = $this->database->escape_string($type);
        $text = $this->database->escape_string($text);
        $reason = $this->database->escape_string($reason);
        $privateText = $this->database->escape_string($privateText);

        $sql = "INSERT INTO " . \LegionBoard\Database::$tableChanges .
                " (" .
                    "teacher," .
                    "course," .
                    "coveringTeacher," .
                    "startingDate," .
                    "startingHour," .
                    "endingDate," .
                    "endingHour," .
                    "type," .
                    "text," .
                    "reason," .
                    "privateText" .
                ")" .
                "VALUES (" .
                    "'$teacher'," .
                    "'$course'," .
                    "'$coveringTeacher'," .
                    "'$startingDate'," .
                    "'$startingHour'," .
                    "'$endingDate'," .
                    "'$endingHour'," .
                    "'$type'," .
                    "'$text'," .
                    "'$reason'," .
                    "'$privateText'" .
                ")";
        if ($this->database->query($sql)) {
            $id = $this->database->insert_id;
            if ($this->activities->log($this->user, \LegionBoard\Activities::ACTION_CREATE, $this->resource, $id)) {
                return $id;
            }
            $this->delete($id);
        }
        return null;
    }

    /**
     * Update a change.
     */
    public function update(
        $id,
        $teacher,
        $course,
        $coveringTeacher,
        $startingDate,
        $startingHour,
        $endingDate,
        $endingHour,
        $type,
        $text,
        $reason,
        $privateText
    ) {
        // Escape strings
        $id = $this->database->escape_string($id);
        $teacher = $this->database->escape_string($teacher);
        $course = $this->database->escape_string($course);
        $coveringTeacher = $this->database->escape_string($coveringTeacher);
        $startingDate = $this->database->escape_string($startingDate);
        $startingHour = $this->database->escape_string($startingHour);
        $endingDate = $this->database->escape_string($endingDate);
        $endingHour = $this->database->escape_string($endingHour);
        $type = $this->database->escape_string($type);
        $text = $this->database->escape_string($text);
        $reason = $this->database->escape_string($reason);
        $privateText = $this->database->escape_string($privateText);

        $sql = "UPDATE " . \LegionBoard\Database::$tableChanges .
                " SET " .
                    "teacher = '$teacher'," .
                    "course = '$course'," .
                    "coveringTeacher = '$coveringTeacher'," .
                    "startingDate = '$startingDate'," .
                    "startingHour = '$startingHour'," .
                    "endingDate = '$endingDate'," .
                    "endingHour = '$endingHour'," .
                    "type = '$type'," .
                    "text = '$text'," .
                    "reason = '$reason'," .
                    "privateText = '$privateText'" .
                "WHERE " .
                    "id = '$id'";
        if ($this->database->query($sql)) {
            $this->activities->log($this->user, \LegionBoard\Activities::ACTION_UPDATE, $this->resource, $id);
            return true;
        }
        return false;
    }

    /**
     * Delete a change.
     */
    public function delete($id)
    {
        $id = $this->database->escape_string($id);
        $sql = "DELETE FROM " . \LegionBoard\Database::$tableChanges . " WHERE id = '$id'";
        if ($this->database->query($sql)) {
            $this->activities->log($this->user, \LegionBoard\Activities::ACTION_DELETE, $this->resource, $id);
            return true;
        }
        return false;
    }
}
