<?php

namespace Rhubarb\Scaffolds\NavigationMenu\Tests\Presenters;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\UnitTestView;
use Rhubarb\Scaffolds\NavigationMenu\Presenters\TwoLevelMenu;
use Rhubarb\Scaffolds\NavigationMenu\Tests\MenuItemTest;

class TwoLevelMenuPresenterTest extends MenuItemTest
{
    public function testMenuViewGetsCorrectMenus()
    {
        $request = Request::current();
        $request->urlPath = "/";

        $view = new UnitTestView();

        $menu = new TwoLevelMenu();
        $menu->AttachMockView($view);

        $menu->test();

        $this->assertCount(5, $view->primaryMenuItems);
        $this->assertCount(0, $view->secondaryMenuItems);

        $this->assertEquals("/companies/", $view->primaryMenuItems[1]->Url);
        $this->assertEquals("/setup/", $view->primaryMenuItems[2]->Url);

        $request->urlPath = "/companies/";

        $menu->test();

        $this->assertCount(5, $view->primaryMenuItems);
        $this->assertEquals("History", $view->secondaryMenuItems[1]->MenuName);

        $request->urlPath = "/companies/history/";

        $menu->test();

        $this->assertCount(5, $view->primaryMenuItems);
        $this->assertEquals("History", $view->secondaryMenuItems[1]->MenuName);

        $request->urlPath = "/companies/history/ancient/";

        $menu->test();

        $this->assertEquals("Ancient History", $view->secondaryMenuItems[0]->MenuName);
        $this->assertEquals(5, $view->activePrimaryMenuItemId);
        $this->assertEquals(8, $view->activeSecondaryMenuItemId);

        $request->urlPath = "/setup/help/closing/";

        $menu->test();

        $this->assertEquals(6, $view->activeSecondaryMenuItemId);
    }
}
