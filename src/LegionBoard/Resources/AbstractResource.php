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

abstract class AbstractResource
{

    /**
     * ID of user accessing API.
     */
    protected $user;

    /**
     * Name of resource.
     */
    protected $resource;

    public function __construct($user)
    {
        $this->user = $user;
        require_once __DIR__ . '/../Database.php';
        $database = new \LegionBoard\Database();
        $this->database = $database->get();
        require_once __DIR__ . '/../Activities.php';
        $this->activities = new \LegionBoard\Activities();
    }
}
