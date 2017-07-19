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

require_once __DIR__ . '/abstractResource.php';

class ActivitiesResource extends AbstractResource
{

    public function __construct($user)
    {
        parent::__construct($user);
        $this->resource = 'activities';
    }

    /**
     * Get one or more activities.
     */
    public function get($id = null)
    {
        $sql = "SELECT * FROM " . Database::$tableActivities;
        // Add where clause for ID
        if (isset($id)) {
            $id = $this->database->escape_string($id);
            $sql .= " WHERE id LIKE '$id'";
        }
        $query = $this->database->query($sql);
        if (!$query || $query->num_rows == 0) {
            return null;
        }
        require_once __DIR__ . '/../authentication.php';
        $this->authentication = new Authentication();
        $activities = array();
        while ($column = $query->fetch_array()) {
            $activity = array(
                            'id' => $column['id'],
                            'user' => $column['user'],
                            'username' => $this->authentication->getUsername($column['user']),
                            'action' => $column['action'],
                            'affectedResource' => $column['affectedResource'],
                            'affectedID' => $column['affectedID'],
                            'time' => $column['time']
                            );
            $activities[] = $activity;
        }
        return $activities;
    }
}
