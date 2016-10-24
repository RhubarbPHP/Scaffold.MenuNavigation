<?php

namespace Rhubarb\Scaffolds\NavigationMenu;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 *
 *
 * @property int $MenuID Repository field
 * @property string $Name Repository field
 * @property-read \TinyTours\BookingApp\Models\TinyToursNavigationMenuItem[]|\Rhubarb\Stem\Collections\RepositoryCollection $MenuItems Relationship
 */
class Menu extends Model
{
    /**
     * @return ModelSchema
     */
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
