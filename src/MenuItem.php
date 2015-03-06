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

use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Int;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Stem\Repositories\MySql\Schema\Index;
use Rhubarb\Stem\Repositories\MySql\Schema\MySqlSchema;

/**
 * Models a menu item
 *
 * @property int $MenuItemID
 * @property int $ParentMenuItemID
 * @property string $MenuName
 * @property string $Url
 * @property string $SecurityOption
 * @property string $ParentMenuItemIDs
 * @property string $CssClassName
 * @property int $Position
 *
 * @property MenuItem[] $Children
 * @property MenuItem $Parent
 */
class MenuItem extends Model
{
    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new MySqlSchema("tblMenuItem");

        $schema->addColumn(
            new AutoIncrement("MenuItemID"),
            new Int("ParentMenuItemID", 0),
            new Varchar("MenuName", 50),
            new Varchar("Url", 200),
            new Varchar("SecurityOption", 200),
            new Varchar("ParentMenuItemIDs", 200),
            new Varchar("CssClassName", 40),
            new Int("Position", 0)
        );

        $schema->addIndex(new Index("ParentMenuItemID", Index::INDEX));

        return $schema;
    }

    public static function getTopLevelMenus()
    {
        $menus = new Collection("MenuItem");
        $menus->filter(new Equals("ParentMenuItemID", 0));
        $menus->replaceSort(
            [
                "Position" => false,
                "MenuName" => true
            ]
        );

        return $menus;
    }

    /**
     * An opportunity for extenders to control visiblity of menu items on a per item basis
     *
     * This allows consumers of the scaffold to override the MenuItem object and
     * integrate with permission systems.
     */
    public function isPermitted()
    {
       return true;
    }

    protected function getChildren()
    {
        $this->clearPropertyCache();

        $children = parent::__get("ChildMenuItems");
        $children->replaceSort(
            [
                "Position" => false,
                "MenuName" => true
            ]
        );

        return $children;
    }

    public static function findByUrl($url)
    {
        return self::findFirst(new Equals("Url", $url));
    }

    private function getAllParents()
    {
        $parents = [];

        $parent = $this->Parent;

        if ($parent !== null) {
            $parents = array_merge($parent->getAllParents(), $parents);
            $parents[] = $parent;
        }

        return $parents;
    }

    protected function beforeSave()
    {
        $parents = $this->getAllParents();
        $parentIds = [];

        foreach ($parents as $parent) {
            $parentIds[] = $parent->MenuItemID;
        }

        $this->ParentMenuItemIDs = implode(",", $parentIds);
    }

    public function getParentMenuItemIDArray()
    {
        return explode(",", $this->ParentMenuItemIDs);
    }
}