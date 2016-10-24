<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Scaffolds\NavigationMenu;

use Rhubarb\Stem\Schema\SolutionSchema;

/**
 * Class NavigationMenuSchema
 * @package Rhubarb\Scaffolds\NavigationMenu
 */
class NavigationMenuSchema extends SolutionSchema
{
    /**
     * NavigationMenuSchema constructor.
     * @param float $version
     */
    public function __construct($version = 0.1)
    {
        parent::__construct($version);

        $this->addModel('Menu', Menu::class, 1);
        $this->addModel("MenuItem", MenuItem::class, 2);
    }

    protected function defineRelationships()
    {
        $this->declareOneToManyRelationships(
            [
                'Menu' => [
                    'MenuItems' => 'MenuItem.MenuID',
                ],
                "MenuItem" => [
                    "ChildMenuItems" => "MenuItem.ParentMenuItemID:Parent"
                ],
            ]
        );
    }
}