<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\MenuBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Mindy\Bundle\MenuBundle\Model\Menu;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207070454 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable(Menu::tableName());
        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('slug', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('url', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('lft', 'integer', ['length' => 11, 'unsigned' => true]);
        $table->addColumn('rgt', 'integer', ['length' => 11, 'unsigned' => true]);
        $table->addColumn('level', 'integer', ['length' => 11, 'unsigned' => true]);
        $table->addColumn('parent_id', 'integer', ['length' => 11, 'unsigned' => true, 'notnull' => false]);
        $table->addColumn('root', 'integer', ['length' => 11, 'unsigned' => true, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(Menu::tableName());
    }
}
