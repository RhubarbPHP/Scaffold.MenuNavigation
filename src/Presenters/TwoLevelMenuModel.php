<?php

namespace Rhubarb\Scaffolds\NavigationMenu\Presenters;

use Rhubarb\Leaf\Leaves\LeafModel;

class TwoLevelMenuModel extends LeafModel
{
    public $primaryMenuItems = [];
    public $secondaryMenuItems = [];
    
    public $primaryContainerClasses = [];
    public $secondaryContainerClasses = [];

    public $activePrimaryMenuItemId;
    public $activeSecondaryMenuItemId;

}