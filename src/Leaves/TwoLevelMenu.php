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
            $menuItem = $this->attemptToMatchMenuItem($currentUrl, $model->primaryMenuItems);
            if ($menuItem) {
                $model->activePrimaryMenuItemId = $menuItem->MenuItemID;
                $model->secondaryMenuItems = $menuItem->Children;
            }
        }

        if (isset($model->activePrimaryMenuItemId) && ($model->activePrimaryMenuItemId !== null)) {
            // Search for and select the secondary item.
            if (count($model->secondaryMenuItems) > 0) {
                $menuItem = $this->attemptToMatchMenuItem($currentUrl, $model->secondaryMenuItems);
                if ($menuItem) {
                    $model->activeSecondaryMenuItemId = $menuItem->MenuItemID;
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

    /**
     * Iterates through the $menuItems array and attempts to pick the best MenuItem object to satisfy the URL.
     *
     * Will return false if a match cannot be found. In cases where there are multiple potential matches at the end
     * of evaluating the URL, will return the first MenuItem it encountered. This can be impacted by sorts on the
     * $menuItems array.
     *
     * @param string $currentUrl The URL to match a MenuItem on
     * @param array $menuItems An array of MenuItem objects
     * @param string $delim The delimiter to split the $currentUrl parameter apart with
     *
     * @return bool|mixed
     */
    protected function attemptToMatchMenuItem($currentUrl, $menuItems, $delim = "/")
    {
        $urlParts = preg_split("@$delim@", $currentUrl, NULL, PREG_SPLIT_NO_EMPTY);
        $potentialMatches = $menuItems;

        while (true) {
            //We either have no more URL parts to go or we only have one potential match
            if (count($urlParts) === 0 || count($potentialMatches) === 1) {
                break;
            }

            $matches = [];
            $urlPartToSearch = array_shift($urlParts);

            foreach ($potentialMatches as $menuItem) {
                if (strpos($menuItem->Url, $urlPartToSearch) !== false) {
                    $matches[] = $menuItem;
                }
            }

            //We only want to update our matches if we have more to search on!
            if (count($matches) > 0) {
                $potentialMatches = $matches;
            }
        }

        return (count($potentialMatches) > 0) ? $potentialMatches[0] : false;
    }
}