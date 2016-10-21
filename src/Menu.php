<?php

namespace Rhubarb\Scaffolds\NavigationMenu;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

class Menu extends Model
{
    protected function createSchema()
    {
        $model = new ModelSchema('tblMenu');

        $model->addColumn(
            new AutoIncrementColumn('MenuID'),
            new StringColumn('Name', 255)
        );

        return $model;
    }
}
