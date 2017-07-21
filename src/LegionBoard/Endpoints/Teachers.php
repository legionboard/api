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
namespace LegionBoard\Endpoints;

require_once __DIR__ . '/AbstractEndpoint.php';

class Teachers extends AbstractEndpoint
{
    
    public function handleGet($seeTimes = false)
    {
        $id = $this->api->getId();
        $teachers = $this->teachers->get($id, $seeTimes);
        if ($teachers != null) {
            return $teachers;
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
        $subjects = $this->api->getFromPost('subjects');
        if ($this->teachers->checkByName($name)) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '301', 'message' => 'A teacher with the given name already exists.')));
        }
        $id = $this->teachers->create($name, $subjects);
        if (isset($id)) {
            $this->api->setStatus(201);
            return array('id' => $id);
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '300', 'message' => 'The teacher could not get created.')));
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
                return array('error' => array(array('code' => '201', 'message' => 'The parameter archived may only contain true or false.')));
        }
        $subjects = $params['subjects'];
        if (!empty($missing)) {
            $this->api->setStatus(400);
            return array('missing' => $missing);
        }
        if ($this->teachers->update($id, $name, $subjects, $archived)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '200', 'message' => 'The teacher could not get updated.')));
    }
    
    public function handleDelete()
    {
        $id = $this->api->getId();
        if ($id == '') {
            $this->api->setStatus(400);
            return array('missing' => array('id'));
        }
        if ($this->changes->get(null, array($id)) != null || $this->changes->get(null, null, null, $id) != null) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '402', 'message' => 'The teacher is still linked to a change.')));
        }
        if ($this->teachers->delete($id)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '400', 'message' => 'The teacher could not get deleted.')));
    }
}
