<?php

namespace Rhubarb\Scaffolds\NavigationMenu\Leaves;

use Rhubarb\Leaf\Leaves\LeafModel;

class TwoLevelMenuModel extends LeafModel
{
    public $primaryMenuItems = [];
    public $secondaryMenuItems = [];

    public $activePrimaryMenuItemId;
    public $activeSecondaryMenuItemId;

}