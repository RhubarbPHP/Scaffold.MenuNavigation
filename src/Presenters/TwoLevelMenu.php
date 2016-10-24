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

namespace Rhubarb\Scaffolds\NavigationMenu\Presenters;

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

        $model->secondaryContainerClasses = $model->primaryContainerClasses = [
            'Nav',
            'primary',
        ];

        return $model;
    }

    /**
     * @param Collection $collection
     */
    public function setPrimaryContainerValues(Collection $collection)
    {
        $currentUrl = Request::current()->urlPath;

        $this->model->primaryMenuItems = $collection;

        $foundActivePrimary = false;

        $parents = null;

        try {
            $menuItem = MenuItem::findByUrl($currentUrl);
            $parents = $menuItem->getParentMenuItemIDArray();

            // See which of the top level menu items best matches the current page.
            foreach ($this->model->primaryMenuItems as $item) {
                if (in_array($item->MenuItemID, $parents) || ($item->Url == $currentUrl)) {
                    $this->model->activePrimaryMenuItemId = $item->MenuItemID;
                    $this->model->secondaryMenuItems = $item->Children;

                    $foundActivePrimary = true;

                    break;
                }
            }
        } catch (RecordNotFoundException $er) {

        }

        if (!$foundActivePrimary) {
            // See which of the top level menu items best matches the current page.
            foreach ($this->model->primaryMenuItems as $item) {
                if (stripos($currentUrl, $item->Url) === 0) {
                    $this->model->activePrimaryMenuItemId = $item->MenuItemID;
                    $this->model->secondaryMenuItems = $item->Children;
                    break;
                }
            }
        }

        if (isset($this->model->activePrimaryMenuItemId) && ($this->model->activePrimaryMenuItemId !== null)) {
            // Search for and select the secondary item.
            foreach ($this->model->secondaryMenuItems as $item) {
                if ($parents !== null) {
                    if (in_array($item->MenuItemID, $parents) || ($item->Url == $currentUrl)) {
                        $this->model->activeSecondaryMenuItemId = $item->MenuItemID;
                        break;
                    }
                } else {
                    if (stripos($currentUrl, $item->Url) === 0) {
                        $this->model->activeSecondaryMenuItemId = $item->MenuItemID;
                        break;
                    }
                }
            }
        }

        $this->model->primaryMenuItems = $this->model->primaryMenuItems->toArray();

        if ($this->model->secondaryMenuItems instanceof Collection) {
            $this->model->secondaryMenuItems = $this->model->secondaryMenuItems->toArray();
        }

        // Process security by removing items which are not permitted.
        $itemsToRemove = [];
        // Remove items that we don't have permission to see.
        foreach ($this->model->primaryMenuItems as $key => $item) {
            if (!$item->isPermitted()) {
                $itemsToRemove[] = $key;
            }
        }

        $this->model->primaryMenuItems = array_diff_key($this->model->primaryMenuItems, $itemsToRemove);

        $itemsToRemove = [];
        // Remove items that we don't have permission to see.
        foreach ($this->model->secondaryMenuItems as $key => $item) {
            if (!$item->isPermitted()) {
                $itemsToRemove[] = $key;
            }
        }

        $this->model->secondaryMenuItems = array_diff_key($this->model->secondaryMenuItems, $itemsToRemove);
    }

    /**
     * @param $classes
     */
    public function setPrimaryContainerClasses($classes)
    {
        $this->model->primaryContainerClasses = $classes;
    }

    /**
     * @param $classes
     */
    public function setSecondaryContainerClasses($classes)
    {
        $this->model->secondaryContainerClasses = $classes;
    }
}