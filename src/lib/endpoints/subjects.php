<?php
/*
 * Copyright (C) 2016 Jan Weber
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
namespace LegionBoard\Endpoints;

require_once __DIR__ . '/abstractEndpoint.php';

class SubjectsEndpoint extends AbstractEndpoint
{

    public function handleGET($seeTimes = false)
    {
        $subjects = $this->subjects->get($this->api->getID(), $seeTimes);
        if ($subjects != null) {
            return $subjects;
        }
        $this->api->setStatus(404);
        return null;
    }

    public function handlePOST()
    {
        $name = self::getFromPOST('name');
        if ($name == '') {
            $this->api->setStatus(400);
            return array('missing' => array('name'));
        }
        $shortcut = self::getFromPOST('shortcut');
        if ($shortcut == '') {
            $this->api->setStatus(400);
            return array('missing' => array('shortcut'));
        }
        if ($this->subjects->checkByName($name)) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '3301', 'message' => 'A subject with the given name already exists.')));
        }
        if ($this->subjects->checkByShortcut($shortcut)) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '3302', 'message' => 'A subject with the given shortcut already exists.')));
        }
        $identification = $this->subjects->create($name, $shortcut);
        if (isset($identification)) {
            $this->api->setStatus(201);
            return array('id' => $identification);
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '3300', 'message' => 'The subject could not get created.')));
    }

    public function handlePUT($params)
    {
        $identification = $this->api->getID();
        $missing = array();
        if ($identification == '') {
            $missing[] = 'id';
        }
        $name = $params['name'];
        if ($name == '') {
            $missing[] = 'name';
        }
        $shortcut = $params['shortcut'];
        if ($shortcut == '') {
            $missing[] = 'shortcut';
        }
        if ($this->subjects->checkByName($name)) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '3202', 'message' => 'A subject with the given name already exists.')));
        }
        if ($this->subjects->checkByShortcut($shortcut)) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '3203', 'message' => 'A subject with the given shortcut already exists.')));
        }
        $archived = $params['archived'];
        switch ($archived) {
            case 'false':
                $archived = '0';
                break;
            case 'true':
                $archived = '1';
                break;
            case '':
                $missing[] = 'archived';
                break;
            default:
                $this->api->setStatus(400);
                return array('error' => array(array('code' => '3201', 'message' => 'The parameter archived may only contain true or false.')));
        }
        if (!empty($missing)) {
            $this->api->setStatus(400);
            return array('missing' => $missing);
        }
        if ($this->subjects->update($identification, $name, $shortcut, $archived)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '3200', 'message' => 'The subject could not get updated.')));
    }

    public function handleDELETE()
    {
        $identification = $this->api->getID();
        if ($identification == '') {
            $this->api->setStatus(400);
            return array('missing' => array('id'));
        }
        if ($this->changes->get(null, null, null, null, null, null, null, null, null, array($identification)) != null) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '3401', 'message' => 'The subject is still linked to a change.')));
        }
        // TODO: Check if subject is linked to a course or teacher
        if ($this->subjects->delete($identification)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '3400', 'message' => 'The subject could not get deleted.')));
    }
}
