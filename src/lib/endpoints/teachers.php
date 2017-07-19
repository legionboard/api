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

require_once __DIR__ . '/abstractEndpoint.php';

class TeachersEndpoint extends AbstractEndpoint
{
    
    public function handleGET($seeTimes = false)
    {
        $identification = $this->api->getID();
        $teachers = $this->teachers->get($identification, $seeTimes);
        if ($teachers != null) {
            return $teachers;
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
        $subjects = self::getFromPOST('subjects');
        if ($this->teachers->checkByName($name)) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '301', 'message' => 'A teacher with the given name already exists.')));
        }
        $identification = $this->teachers->create($name, $subjects);
        if (isset($identification)) {
            $this->api->setStatus(201);
            return array('id' => $identification);
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '300', 'message' => 'The teacher could not get created.')));
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
        if ($this->teachers->update($identification, $name, $subjects, $archived)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '200', 'message' => 'The teacher could not get updated.')));
    }
    
    public function handleDELETE()
    {
        $identification = $this->api->getID();
        if ($identification == '') {
            $this->api->setStatus(400);
            return array('missing' => array('id'));
        }
        if ($this->changes->get(null, array($identification)) != null || $this->changes->get(null, null, null, $identification) != null) {
            $this->api->setStatus(400);
            return array('error' => array(array('code' => '402', 'message' => 'The teacher is still linked to a change.')));
        }
        if ($this->teachers->delete($identification)) {
            $this->api->setStatus(204);
            return null;
        }
        $this->api->setStatus(409);
        return array('error' => array(array('code' => '400', 'message' => 'The teacher could not get deleted.')));
    }
}
