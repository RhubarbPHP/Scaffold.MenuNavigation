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

namespace Rhubarb\Scaffolds\NavigationMenu\Leaves;

use Rhubarb\Leaf\Views\View;

class TwoLevelMenuView extends View
{
    /**
     * @var TwoLevelMenuModel
     */
    protected $model;

    protected $requiresContainerDiv = false;
    protected $requiresStateInput = false;

    protected function printViewContent()
    {
        ?>
        <ul class='Nav primary'>
            <?php

            foreach ($this->model->primaryMenuItems as $menuItem) {
                $classes = [];

                if ($menuItem->MenuItemID == $this->model->activePrimaryMenuItemId) {
                    $classes[] = "-selected";
                }

                if ($menuItem->CssClassName != "") {
                    $classes[] = $menuItem->CssClassName;
                }

                $class = (sizeof($classes) > 0) ? " class=\"" . implode(" ", $classes) . "\"" : "";

                print "<li{$class}><a href=\"{$menuItem->Url}\"><span class=\"icon\"></span>{$menuItem->MenuName}</a></li>";
            }

            ?>
        </ul>
        <ul class='Nav secondary'>
            <?php

            foreach ($this->model->secondaryMenuItems as $menuItem) {
                $classes = [];

                if ($menuItem->MenuItemID == $this->model->activeSecondaryMenuItemId) {
                    $classes[] = "-selected";
                }

                if ($menuItem->CssClassName != "") {
                    $classes[] = $menuItem->CssClassName;
                }

                $class = (sizeof($classes) > 0) ? " class=\"" . implode(" ", $classes) . "\"" : "";

                print "<li {$class}><a href=\"{$menuItem->Url}\"><span class=\"icon\"></span>{$menuItem->MenuName}</a></li>";
            }

            ?>
        </ul>
        <?php
    }
}
