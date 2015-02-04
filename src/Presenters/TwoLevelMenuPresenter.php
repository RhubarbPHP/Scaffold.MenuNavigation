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

use Rhubarb\Crown\Context;
use Rhubarb\Scaffolds\NavigationMenu\MenuItem;
use Rhubarb\Leaf\Presenters\Presenter;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;

class TwoLevelMenuPresenter extends Presenter
{
    protected function createView()
    {
        return new TwoLevelMenuView();
    }

    protected function applyModelToView()
    {
        parent::applyModelToView();

        $currentUrl = Context::currentRequest()->UrlPath;

        $this->view->primaryMenuItems = MenuItem::getTopLevelMenus();
        $this->view->secondaryMenuItems = [];

        $foundActivePrimary = false;

        $parents = null;

        try {
            $menuItem = MenuItem::findByUrl($currentUrl);
            $parents = $menuItem->getParentMenuItemIDArray();

            // See which of the top level menu items best matches the current page.
            foreach ($this->view->primaryMenuItems as $item) {
                if (in_array($item->MenuItemID, $parents) || ($item->Url == $currentUrl)) {
                    $this->view->activePrimaryMenuItemId = $item->MenuItemID;
                    $this->view->secondaryMenuItems = $item->Children;

                    $foundActivePrimary = true;

                    break;
                }
            }
        } catch (RecordNotFoundException $er) {

        }

        if (!$foundActivePrimary) {
            // See which of the top level menu items best matches the current page.
            foreach ($this->view->primaryMenuItems as $item) {
                if (stripos($currentUrl, $item->Url) === 0) {
                    $this->view->activePrimaryMenuItemId = $item->MenuItemID;
                    $this->view->secondaryMenuItems = $item->Children;
                    break;
                }
            }
        }

        if (isset($this->view->activePrimaryMenuItemId) && ($this->view->activePrimaryMenuItemId !== null)) {
            // Search for and select the secondary item.
            foreach ($this->view->secondaryMenuItems as $item) {
                if ($parents !== null) {
                    if (in_array($item->MenuItemID, $parents) || ($item->Url == $currentUrl)) {
                        $this->view->activeSecondaryMenuItemId = $item->MenuItemID;
                        break;
                    }
                } else {
                    if (stripos($currentUrl, $item->Url) === 0) {
                        $this->view->activeSecondaryMenuItemId = $item->MenuItemID;
                        break;
                    }
                }
            }
        }
    }
}