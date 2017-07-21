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

abstract class AbstractEndpoint
{

    /**
     * Property: api
     * API basing on AbstractApi.
     */
    protected $api = null;

    public function __construct($api, $user)
    {
        $this->api = $api;
        require_once __DIR__ . '/../Resources/Activities.php';
        $this->activities = new \LegionBoard\Resources\Activities($user);
        require_once __DIR__ . '/../Resources/Changes.php';
        $this->changes = new \LegionBoard\Resources\Changes($user);
        require_once __DIR__ . '/../Resources/Courses.php';
        $this->courses = new \LegionBoard\Resources\Courses($user);
        require_once __DIR__ . '/../Resources/Teachers.php';
        $this->teachers = new \LegionBoard\Resources\Teachers($user);
        require_once __DIR__ . '/../Resources/Subjects.php';
        $this->subjects = new \LegionBoard\Resources\Subjects($user);
    }

    /**
     * Replaces aliases.
     */
    protected static function replaceAlias($alias)
    {
        switch ($alias) {
            case 'now':
                return substr(date('c'), 0, 10);
            case 'tom':
                return substr(date('c', time() + 86400), 0, 10);
            case 'i3d':
                return substr(date('c', time() + 259200), 0, 10);
            case 'i1w':
                return substr(date('c', time() + 604800), 0, 10);
            case 'i1m':
                return substr(date('c', time() + 2419200), 0, 10);
            case 'i1y':
                return substr(date('c', time() + 31536000), 0, 10);
            default:
                return null;
        }
    }
}
