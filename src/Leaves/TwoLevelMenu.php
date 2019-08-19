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

use Rhubarb\Crown\Request\Request;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\NavigationMenu\MenuItem;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;

class TwoLevelMenu extends Leaf
{
    /**
     * @var TwoLevelMenuModel
     */
    protected $model;

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return TwoLevelMenuView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new TwoLevelMenuModel();

        $currentUrl = Request::current()->urlPath;

        $model->primaryMenuItems = MenuItem::getTopLevelMenus();
        $model->secondaryMenuItems = [];

        $foundActivePrimary = false;

        $parents = null;

        try {
            $menuItem = MenuItem::findByUrl($currentUrl);
            $parents = $menuItem->getParentMenuItemIDArray();

            // See which of the top level menu items best matches the current page.
            foreach ($model->primaryMenuItems as $item) {
                if (in_array($item->MenuItemID, $parents) || ($item->Url == $currentUrl)) {
                    $model->activePrimaryMenuItemId = $item->MenuItemID;
                    $model->secondaryMenuItems = $item->Children;

                    $foundActivePrimary = true;

                    break;
                }
            }
        } catch (RecordNotFoundException $er) {

        }

        if (!$foundActivePrimary) {
            // See which of the top level menu items best matches the current page.
//            foreach ($model->primaryMenuItems as $item) {
//                if (stripos($currentUrl, $item->Url) === 0) {
//                    $model->activePrimaryMenuItemId = $item->MenuItemID;
//                    $model->secondaryMenuItems = $item->Children;
//                    break;
//                }
//            }

            $menuItem = $this->attemptToMatchParentMenuItem($currentUrl, $model->primaryMenuItems);
            if ($menuItem) {
                $model->activePrimaryMenuItemId = $menuItem->MenuItemID;
                $model->secondaryMenuItems = $menuItem->Children;
            }
        }

        if (isset($model->activePrimaryMenuItemId) && ($model->activePrimaryMenuItemId !== null)) {
            // Search for and select the secondary item.
            foreach ($model->secondaryMenuItems as $item) {
                if ($parents !== null) {
                    if (in_array($item->MenuItemID, $parents) || ($item->Url == $currentUrl)) {
                        $model->activeSecondaryMenuItemId = $item->MenuItemID;
                        break;
                    }
                } else {
                    if (stripos($currentUrl, $item->Url) === 0) {
                        $model->activeSecondaryMenuItemId = $item->MenuItemID;
                        break;
                    }
                }
            }
        }

        $model->primaryMenuItems = $model->primaryMenuItems->toArray();

        if ($model->secondaryMenuItems instanceof Collection) {
            $model->secondaryMenuItems = $model->secondaryMenuItems->toArray();
        }

        // Process security by removing items which are not permitted.
        $itemsToRemove = [];
        // Remove items that we don't have permission to see.
        foreach ($model->primaryMenuItems as $key => $item) {
            if (!$item->isPermitted()) {
                $itemsToRemove[] = $key;
            }
        }

        $model->primaryMenuItems = array_diff_key($model->primaryMenuItems, $itemsToRemove);

        $itemsToRemove = [];
        // Remove items that we don't have permission to see.
        foreach ($model->secondaryMenuItems as $key => $item) {
            if (!$item->isPermitted()) {
                $itemsToRemove[] = $key;
            }
        }

        $model->secondaryMenuItems = array_diff_key($model->secondaryMenuItems, $itemsToRemove);

        return $model;
    }

    protected function attemptToMatchParentMenuItem($currentUrl, $primaryMenuItems, $delim = "/")
    {
        $urlParts = explode($delim, $currentUrl);
        $potentialMatches = $primaryMenuItems;

        while (true) {
            if (count($urlParts) === 0 || count($potentialMatches) === 0) {
                return false;
            }

            $matches = [];
            $urlPartToSearch = array_shift($urlParts);

            if (empty($urlPartToSearch)) {
                continue;
            }

            foreach ($potentialMatches as $primaryMenuItem) {
                if (strpos($primaryMenuItem->Url, $urlPartToSearch) !== false) {
                    $matches[] = $primaryMenuItem;
                }
            }

            $potentialMatches = $matches;
            if (count($potentialMatches) === 1) {
                return $potentialMatches[0];
            }
        }

        return false;
    }
}