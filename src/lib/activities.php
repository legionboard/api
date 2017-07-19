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
namespace LegionBoard\Lib;

class Activities
{

    /**
     * Index keys for actions.
     */
    const ACTION_CREATE = 0;
    const ACTION_UPDATE = 1;
    const ACTION_DELETE = 2;
    

    /**
     * Connect with the database.
     */
    public function __construct()
    {
        require_once __DIR__ . '/database.php';
        $database = new Database();
        $this->db = $database->get();
    }

    /**
     * Log an action.
     */
    public function log($user, $action, $affectedResource, $affectedID)
    {
        $user = $this->db->escape_string($user);
        $action= $this->db->escape_string($action);
        $affectedResource= $this->db->escape_string($affectedResource);
        $affectedID= $this->db->escape_string($affectedID);
        $sql = "INSERT INTO " . Database::$tableActivities .
                " (" .
                    "user," .
                    "action," .
                    "affectedResource," .
                    "affectedID" .
                ") " .
                "VALUES (" .
                    "'$user'," .
                    "'$action'," .
                    "'$affectedResource'," .
                    "'$affectedID'" .
                ")";
        return $this->db->query($sql);
    }
}
