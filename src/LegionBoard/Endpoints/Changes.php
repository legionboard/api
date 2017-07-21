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
 *
 * Endpoint: changes
 * Accepts: GET, PUT, POST, DELETE, OPTIONS
 */
namespace LegionBoard\Endpoints;

require_once __DIR__ . '/AbstractEndpoint.php';

class Changes extends AbstractEndpoint
{

    /**
     * Handles GET requests.
     */
    public function handleGet($seeReasons = false, $seePrivateTexts = false, $seeTimes = false)
    {
        $givenTeachers = explode(",", $this->api->getFromGet('teachers'));
        foreach ($givenTeachers as $teacher) {
            if ($teacher != '' && $teacher != 0) {
                if (!ctype_digit($teacher)) {
                    $error[] = array('code' => '1100', 'message' => 'The teacher may only contain an integer.');
                    break;
                } elseif (!$this->teachers->checkById($teacher)) {
                    $error[] = array('code' => '1101', 'message' => 'The teacher does not exist.');
                    break;
                }
            }
        }
        $givenCourses = explode(",", $this->api->getFromGet('courses'));
        foreach ($givenCourses as $course) {
            if ($course != '' && $course != 0) {
                if (!ctype_digit($course)) {
                    $error[] = array('code' => '1100', 'message' => 'The course may only contain an integer.');
                    break;
                } elseif (!$this->courses->checkById($course)) {
                    $error[] = array('code' => '1101', 'message' => 'The course does not exist.');
                    break;
                }
            }
        }
        $coveringTeacher = $this->api->getFromGet('coveringTeacher');
        if ($coveringTeacher != '') {
            if (!ctype_digit($coveringTeacher)) {
                $error[] = array('code' => '1102', 'message' => 'The covering teacher may only contain an integer.');
            } elseif (!$this->teachers->checkById($coveringTeacher)) {
                $error[] = array('code' => '1103', 'message' => 'The covering teacher does not exist.');
            }
        }
        $startBy = $this->api->getFromGet('startBy');
        // Replace alias with time
        if ($startBy != '') {
            if (self::replaceAlias($startBy) != null) {
                $startBy = self::replaceAlias($startBy);
            }
            if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startBy)) {
                $error[] = array('code' => '1104', 'message' => 'The starting date is formatted badly.');
            } elseif (!checkdate(substr($startBy, 5, 2), substr($startBy, 8, 2), substr($startBy, 0, 4))) {
                $error[] = array('code' => '1105', 'message' => 'The starting date does not exist.');
            }
        }
        $endBy = $this->api->getFromGet('endBy');
        // Replace alias with time
        if ($endBy != '') {
            if (self::replaceAlias($endBy) != null) {
                $endBy = self::replaceAlias($endBy);
            }
            if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endBy)) {
                $error[] = array('code' => '1106', 'message' => 'The ending date is formatted badly.');
            } elseif (!checkdate(substr($endBy, 5, 2), substr($endBy, 8, 2), substr($endBy, 0, 4))) {
                $error[] = array('code' => '1107', 'message' => 'The ending date does not exist.');
            }
        }
        if (!empty($error)) {
            $this->api->setStatus(400);
            return array('error' => $error);
        }
        if ($startBy != '' && $endBy != '') {
            $datetime1 = new \DateTime(substr($startBy, 0, 10));
            $datetime2 = new \DateTime(substr($endBy, 0, 10));
            if ($datetime1 > $datetime2) {
                $error[] = array('code' => '1108', 'message' => 'The ending date has to be after the starting date.');
            }
        }
        if (!empty($error)) {
            $this->api->setStatus(400);
            return array('error' => $error);
        }
        $changes = $this->changes->get($this->api->getID(), $givenTeachers, $givenCourses, $coveringTeacher, $startBy, $endBy, $seeReasons, $seePrivateTexts, $seeTimes);
        $this->api->setStatus(($changes == null) ? 404 : 200);
        return $changes;
    }

    /**
     * Handles POST requests.
     */
    public function handlePOST()
    {
        $missing = array();
        $teacher = $this->api->getFromPost('teacher');
        $startingDate = $this->api->getFromPost('startingDate');
        if ($startingDate == '') {
            $missing[] = 'startingDate';
        }
        $endingDate = $this->api->getFromPost('endingDate');
        if ($endingDate == '') {
            $missing[] = 'endingDate';
        }
        $type = $this->api->getFromPost('type');
        if ($type == '') {
            $missing[] = 'type';
        }
        $coveringTeacher = $this->api->getFromPost('coveringTeacher');
        if ($type == 1 && $coveringTeacher == '') {
            $missing[] = 'coveringTeacher';
        }
        $text = $this->api->getFromPost('text');
        if ($type == 2 && $text == '') {
            $missing[] = 'text';
        }
        $privateText = $this->api->getFromPost('privateText');
        $course = $this->api->getFromPost('course');
        $startingHour = $this->api->getFromPost('startingHour');
        $endingHour = $this->api->getFromPost('endingHour');
        if (!empty($missing)) {
            $this->api->setStatus(400);
            return array('missing' => $missing);
        }

        $error = array();
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startingDate)) {
            $error[] = array('code' => '1301', 'message' => 'The starting date is formatted badly.');
        } elseif (!checkdate(substr($startingDate, 5, 2), substr($startingDate, 8, 2), substr($startingDate, 0, 4))) {
            $error[] = array('code' => '1303', 'message' => 'The starting date does not exist.');
        }
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endingDate)) {
            $error[] = array('code' => '1302', 'message' => 'The ending date is formatted badly.');
        } elseif (!checkdate(substr($endingDate, 5, 2), substr($endingDate, 8, 2), substr($endingDate, 0, 4))) {
            $error[] = array('code' => '1304', 'message' => 'The ending date does not exist.');
        }
        if ($teacher != '' && !ctype_digit($teacher)) {
            $error[] = array('code' => '1305', 'message' => 'The teacher may only contain an integer.');
        } elseif ($teacher != '' && !$this->teachers->checkById($teacher)) {
            $error[] = array('code' => '1308', 'message' => 'The teacher does not exist.');
        }
        if ($course != '' && !ctype_digit($course)) {
            $error[] = array('code' => '1305', 'message' => 'The course may only contain an integer.');
        } elseif ($course != '' && !$this->courses->checkById($course)) {
            $error[] = array('code' => '1308', 'message' => 'The course does not exist.');
        }
        if ($coveringTeacher != '' && !ctype_digit($coveringTeacher)) {
            $error[] = array('code' => '1306', 'message' => 'The covering teacher may only contain an integer.');
        } elseif ($coveringTeacher != '' && !$this->teachers->checkById($coveringTeacher)) {
            $error[] = array('code' => '1309', 'message' => 'The covering teacher does not exist.');
        }
        if ($type != '0' && $type != '1' && $type != '2' && $type != '3') {
            $error[] = array('code' => '1307', 'message' => 'The type is not allowed.');
        }
        $reason = $this->api->getFromPost('reason');
        if ($reason != '' && $reason != '0' && $reason != '1' && $reason != '2') {
            $error[] = array('code' => '1311', 'message' => 'The reason is not allowed.');
        }
        if (!empty($error)) {
            $this->api->setStatus(400);
            return array('error' => $error);
        }
        $datetime1 = new \DateTime($startingDate);
        $datetime2 = new \DateTime($endingDate);
        if ($datetime1 > $datetime2) {
            $error[] = array('code' => '1310', 'message' => 'The ending date has to be after the starting date.');
        }
        if (!empty($error)) {
            $this->api->setStatus(400);
            return array('error' => $error);
        }
        $identification = $this->changes->create($teacher, $course, $coveringTeacher, $startingDate, $startingHour, $endingDate, $endingHour, $type, $text, $reason, $privateText);
        if (isset($identification)) {
            $this->api->setStatus(201);
            return array('id' => $identification);
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '1300', 'message' => 'The change could not get created.')));
    }
    
    /**
     * Handles PUT requests.
     */
    public function handlePUT($params)
    {
        $missing = array();
        $identification = $this->api->getID();
        if ($identification == '') {
            $missing[] = 'id';
        }
        $teacher = $params['teacher'];
        $startingDate = $params['startingDate'];
        if ($startingDate == '') {
            $missing[] = 'startingDate';
        }
        $endingDate = $params['endingDate'];
        if ($endingDate == '') {
            $missing[] = 'endingDate';
        }
        $type = $params['type'];
        if ($type == '') {
            $missing[] = 'type';
        }
        $coveringTeacher = $params['coveringTeacher'];
        if ($type == 1 && $coveringTeacher == '') {
            $missing[] = 'coveringTeacher';
        }
        $text = $params['text'];
        if ($type == 2 && $text == '') {
            $missing[] = 'text';
        }
        $privateText = $params['privateText'];
        $course = $params['course'];
        $startingHour = $params['startingHour'];
        $endingHour = $params['endingHour'];
        if (!empty($missing)) {
            $this->api->setStatus(400);
            return array('missing' => $missing);
        }

        $error = array();
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startingDate)) {
            $error[] = array('code' => '1201', 'message' => 'The starting date is formatted badly.');
        } elseif (!checkdate(substr($startingDate, 5, 2), substr($startingDate, 8, 2), substr($startingDate, 0, 4))) {
            $error[] = array('code' => '1203', 'message' => 'The starting date does not exist.');
        }
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endingDate)) {
            $error[] = array('code' => '1202', 'message' => 'The ending date is formatted badly.');
        } elseif (!checkdate(substr($endingDate, 5, 2), substr($endingDate, 8, 2), substr($endingDate, 0, 4))) {
            $error[] = array('code' => '1204', 'message' => 'The ending date does not exist.');
        }
        if ($teacher != '' && !ctype_digit($teacher)) {
            $error[] = array('code' => '1205', 'message' => 'The teacher may only contain an integer.');
        } elseif ($teacher != '' && !$this->teachers->checkById($teacher)) {
            $error[] = array('code' => '1208', 'message' => 'The teacher does not exist.');
        }
        if ($course != '' && !ctype_digit($course)) {
            $error[] = array('code' => '1205', 'message' => 'The course may only contain an integer.');
        } elseif ($course != '' && !$this->courses->checkById($course)) {
            $error[] = array('code' => '1208', 'message' => 'The course does not exist.');
        }
        if ($coveringTeacher != '' && !ctype_digit($coveringTeacher)) {
            $error[] = array('code' => '1206', 'message' => 'The covering teacher may only contain an integer.');
        } elseif ($coveringTeacher != '' && !$this->teachers->checkById($coveringTeacher)) {
            $error[] = array('code' => '1209', 'message' => 'The covering teacher does not exist.');
        }
        if ($type != '0' && $type != '1' && $type != '2' && $type != '3') {
            $error[] = array('code' => '1207', 'message' => 'The type is not allowed.');
        }
        $reason = $params['reason'];
        if ($reason != '' && $reason != '0' && $reason != '1' && $reason != '2') {
            $error[] = array('code' => '1211', 'message' => 'The reason is not allowed.');
        }
        if (!empty($error)) {
            $this->api->setStatus(400);
            return array('error' => $error);
        }
        $datetime1 = new \DateTime($startingDate);
        $datetime2 = new \DateTime($endingDate);
        if ($datetime1 > $datetime2) {
            $error[] = array('code' => '1210', 'message' => 'The ending date has to be after the starting date.');
        }
        if (!empty($error)) {
            $this->api->setStatus(400);
            return array('error' => $error);
        }
        if ($this->changes->update($identification, $teacher, $course, $coveringTeacher, $startingDate, $startingHour, $endingDate, $endingHour, $type, $text, $reason, $privateText)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '1200', 'message' => 'The change could not get updated.')));
    }
    
    /**
     * Handles DELETE requests.
     */
    public function handleDELETE()
    {
        if ($this->api->getID() == '') {
            $this->api->setStatus(400);
            return array('missing' => array('id'));
        }
        if ($this->changes->delete($this->api->getID())) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '1400', 'message' => 'The change could not get deleted.')));
    }
}
