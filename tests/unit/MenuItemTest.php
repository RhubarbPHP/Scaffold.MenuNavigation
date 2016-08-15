<?php

namespace Rhubarb\Scaffolds\NavigationMenu\Tests;

use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Scaffolds\NavigationMenu\MenuItem;
use Rhubarb\Scaffolds\NavigationMenu\NavigationMenuSchema;
use Rhubarb\Stem\Schema\SolutionSchema;

class MenuItemTest extends RhubarbTestCase
{
    public function setUp()
    {
        parent::setUp();

        SolutionSchema::registerSchema("NavigationMenu", NavigationMenuSchema::class);

        MenuItem::clearObjectCache();

        $companies = new MenuItem();
        $companies->Url = "/companies/";
        $companies->MenuName = "Companies";
        $companies->save();

        $contacts = new MenuItem();
        $contacts->Url = "/companies/contacts/";
        $contacts->MenuName = "Contacts";
        $contacts->save();

        $companies->Children->append($contacts);

        $history = new MenuItem();
        $history->Url = "/companies/history/";
        $history->MenuName = "History";
        $history->save();

        $companies->Children->append($history);

        $founders = new MenuItem();
        $founders->Url = "/companies/history/founders/";
        $founders->MenuName = "Founds";
        $founders->save();

        $history->Children->append($founders);

        $setup = new MenuItem();
        $setup->Url = "/setup/";
        $setup->MenuName = "Setup";
        $setup->save();

        $help = new MenuItem();
        $help->Url = "/setup/help/";
        $help->MenuName = "Help";
        $help->save();

        $setup->Children->append($help);

        $help2 = new MenuItem();
        $help2->Url = "/setup/help/closing/";
        $help2->MenuName = "Closing";
        $help2->save();

        $help->Children->append($help2);

        $ancientHistory = new MenuItem();
        $ancientHistory->Url = "/companies/history/ancient/";
        $ancientHistory->MenuName = "Ancient History";
        $ancientHistory->save();

        $setup->Children->append($ancientHistory);

        $menu = new MenuItem();
        $menu->MenuName = "dompanies";
        $menu->Url = "empty";
        $menu->save();

        $menu = new MenuItem();
        $menu->MenuName = "eompanies";
        $menu->Position = 100;
        $menu->Url = "empty";
        $menu->save();

        $menu = new MenuItem();
        $menu->MenuName = "fompanies";
        $menu->Url = "empty";
        $menu->save();

        $subMenu = new MenuItem();
        $subMenu->MenuName = "a";
        $subMenu->Url = "empty";
        $subMenu->save();

        $menu->Children->append($subMenu);

        $subMenu = new MenuItem();
        $subMenu->MenuName = "b";
        $subMenu->Url = "empty";
        $subMenu->Position = 50;
        $subMenu->save();

        $menu->Children->append($subMenu);

        $subMenu = new MenuItem();
        $subMenu->MenuName = "c";
        $subMenu->Url = "empty";
        $subMenu->Position = 100;
        $subMenu->save();

        $menu->Children->append($subMenu);
    }

    public function testParentage()
    {
        $menu = MenuItem::findByUrl("/companies/history/founders/");
        $this->assertEquals("1,3", $menu->ParentMenuItemIDs);
    }

    public function testTopLevelMenus()
    {
        $menus = MenuItem::getTopLevelMenus();

        $this->assertCount(5, $menus);
        $this->assertEquals("Setup", $menus[2]->MenuName);
    }

    public function testMenuIsSorted()
    {
        $menus = MenuItem::getTopLevelMenus();

        $this->assertEquals("eompanies", $menus[0]->MenuName);
        $this->assertEquals("Companies", $menus[1]->MenuName);

        $this->assertEquals("c", $menus[4]->Children[0]->MenuName);
        $this->assertEquals("b", $menus[4]->Children[1]->MenuName);
    }
}
