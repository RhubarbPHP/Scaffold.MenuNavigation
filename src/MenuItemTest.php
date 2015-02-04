<?php

namespace Rhubarb\Scaffolds\NavigationMenu;

use Rhubarb\Crown\UnitTesting\CoreTestCase;

class MenuItemTest extends CoreTestCase
{
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		MenuItem::ClearObjectCache();

		$companies = new MenuItem();
		$companies->Url = "/companies/";
		$companies->MenuName = "Companies";
		$companies->Save();

		$contacts = new MenuItem();
		$contacts->Url = "/companies/contacts/";
		$contacts->MenuName = "Contacts";
		$contacts->Save();

		$companies->Children->Append( $contacts );

		$history = new MenuItem();
		$history->Url = "/companies/history/";
		$history->MenuName = "History";
		$history->Save();

		$companies->Children->Append( $history );

		$founders = new MenuItem();
		$founders->Url = "/companies/history/founders/";
		$founders->MenuName = "Founds";
		$founders->Save();

		$history->Children->Append( $founders );

		$setup = new MenuItem();
		$setup->Url = "/setup/";
		$setup->MenuName = "Setup";
		$setup->Save();

		$help = new MenuItem();
		$help->Url = "/setup/help/";
		$help->MenuName = "Help";
		$help->Save();

		$setup->Children->Append( $help );

		$help2 = new MenuItem();
		$help2->Url = "/setup/help/closing/";
		$help2->MenuName = "Closing";
		$help2->Save();

		$help->Children->Append( $help2 );

		$ancientHistory = new MenuItem();
		$ancientHistory->Url = "/companies/history/ancient/";
		$ancientHistory->MenuName = "Ancient History";
		$ancientHistory->Save();

		$setup->Children->Append( $ancientHistory );

		$menu = new MenuItem();
		$menu->MenuName = "dompanies";
		$menu->Url = "empty";
		$menu->Save();

		$menu = new MenuItem();
		$menu->MenuName = "eompanies";
		$menu->Position = 100;
		$menu->Url = "empty";
		$menu->Save();

		$menu = new MenuItem();
		$menu->MenuName = "fompanies";
		$menu->Url = "empty";
		$menu->Save();

		$subMenu = new MenuItem();
		$subMenu->MenuName = "a";
		$subMenu->Url = "empty";
		$subMenu->Save();

		$menu->Children->Append( $subMenu );

		$subMenu = new MenuItem();
		$subMenu->MenuName = "b";
		$subMenu->Url = "empty";
		$subMenu->Position = 50;
		$subMenu->Save();

		$menu->Children->Append( $subMenu );

		$subMenu = new MenuItem();
		$subMenu->MenuName = "c";
		$subMenu->Url = "empty";
		$subMenu->Position = 100;
		$subMenu->Save();

		$menu->Children->Append( $subMenu );
	}

	public function testParentage()
	{
		$menu = MenuItem::FindByUrl( "/companies/history/founders/" );
		$this->assertEquals( "1,3", $menu->ParentMenuItemIDs );
	}

	public function testTopLevelMenus()
	{
		$menus = MenuItem::GetTopLevelMenus();

		$this->assertCount( 5, $menus );
		$this->assertEquals( "Setup", $menus[2]->MenuName );
	}

	public function testMenuIsSorted()
	{
		$menus = MenuItem::GetTopLevelMenus();

		$this->assertEquals( "eompanies", $menus[0]->MenuName );
		$this->assertEquals( "Companies", $menus[1]->MenuName );

		$this->assertEquals( "c", $menus[ 4 ]->Children[0]->MenuName );
		$this->assertEquals( "b", $menus[ 4 ]->Children[1]->MenuName );
	}
}
