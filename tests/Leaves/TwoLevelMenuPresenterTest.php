<?php

namespace Rhubarb\Scaffolds\NavigationMenu\Tests\Presenters;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Scaffolds\NavigationMenu\Presenters\TwoLevelMenu;
use Rhubarb\Scaffolds\NavigationMenu\Presenters\TwoLevelMenuModel;
use Rhubarb\Scaffolds\NavigationMenu\Tests\MenuItemTest;

/**
 * Class TwoLevelMenuPresenterTest
 * @package Rhubarb\Scaffolds\NavigationMenu\Tests\Presenters
 */
class TwoLevelMenuPresenterTest extends MenuItemTest
{
    public function testMenuViewGetsCorrectMenus()
    {
        $request = Request::current();
        $request->urlPath = "/";

        $menu = new TwoLevelMenu();

        /** @var TwoLevelMenuModel $menuModel */
        $menuModel = $menu->getModelForTesting();

        $this->assertCount(5, $menuModel->primaryMenuItems);
        $this->assertCount(0, $menuModel->secondaryMenuItems);

        $this->assertEquals("/companies/", $menuModel->primaryMenuItems[1]->Url);
        $this->assertEquals("/setup/", $menuModel->primaryMenuItems[2]->Url);

        $request->urlPath = "/companies/";

        $menu = new TwoLevelMenu();

        /** @var TwoLevelMenuModel $menuModel */
        $menuModel = $menu->getModelForTesting();

        $this->assertCount(5, $menuModel->primaryMenuItems);
        $this->assertEquals("History", $menuModel->secondaryMenuItems[1]->MenuName);

        $request->urlPath = "/companies/history/";

        $menu = new TwoLevelMenu();

        /** @var TwoLevelMenuModel $menuModel */
        $menuModel = $menu->getModelForTesting();

        $this->assertCount(5, $menuModel->primaryMenuItems);
        $this->assertEquals("History", $menuModel->secondaryMenuItems[1]->MenuName);

        $request->urlPath = "/companies/history/ancient/";

        $menu = new TwoLevelMenu();

        /** @var TwoLevelMenuModel $menuModel */
        $menuModel = $menu->getModelForTesting();

        $this->assertEquals("Ancient History", $menuModel->secondaryMenuItems[0]->MenuName);
        $this->assertEquals(5, $menuModel->activePrimaryMenuItemId);
        $this->assertEquals(8, $menuModel->activeSecondaryMenuItemId);

        $request->urlPath = "/setup/help/closing/";

        $menu = new TwoLevelMenu();

        /** @var TwoLevelMenuModel $menuModel */
        $menuModel = $menu->getModelForTesting();

        $this->assertEquals(6, $menuModel->activeSecondaryMenuItemId);
    }
}
