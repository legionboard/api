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

require_once __DIR__ . '/AbstractEndpoint.php';

class Subjects extends AbstractEndpoint
{

    public function handleGet($seeTimes = false)
    {
        $subjects = $this->subjects->get($this->api->getId(), $seeTimes);
        if ($subjects != null) {
            return $subjects;
        }
        $this->api->setStatus(404);
        return null;
    }

    public function handlePost()
    {
        $name = $this->api->getFromPost('name');
        if ($name == '') {
            $this->api->setStatus(400);
            return array('missing' => array('name'));
        }
        $shortcut = $this->api->getFromPost('shortcut');
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
        $id = $this->subjects->create($name, $shortcut);
        if (isset($id)) {
            $this->api->setStatus(201);
            return array('id' => $id);
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '3300', 'message' => 'The subject could not get created.')));
    }

    public function handlePut($params)
    {
        $id = $this->api->getId();
        $missing = array();
        if ($id == '') {
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
        if ($this->subjects->update($id, $name, $shortcut, $archived)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '3200', 'message' => 'The subject could not get updated.')));
    }

    public function handleDelete()
    {
        $id = $this->api->getId();
        if ($id == '') {
            $this->api->setStatus(400);
            return array('missing' => array('id'));
        }
        if ($this->changes->get(null, null, null, null, null, null, null, null, null, array($id)) != null) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '3401', 'message' => 'The subject is still linked to a change.')));
        }
        // TODO: Check if subject is linked to a course or teacher
        if ($this->subjects->delete($id)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '3400', 'message' => 'The subject could not get deleted.')));
    }
}
