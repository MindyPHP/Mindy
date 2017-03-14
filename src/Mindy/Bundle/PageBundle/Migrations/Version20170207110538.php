<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Mindy\Bundle\PageBundle\Model\Page;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207110538 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable(Page::tableName());
        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('url', 'string', ['length' => 255]);
        $table->addColumn('lft', 'integer', ['length' => 11, 'unsigned' => true]);
        $table->addColumn('rgt', 'integer', ['length' => 11, 'unsigned' => true]);
        $table->addColumn('level', 'integer', ['length' => 11, 'unsigned' => true]);
        $table->addColumn('parent_id', 'integer', ['length' => 11, 'unsigned' => true, 'notnull' => false]);
        $table->addColumn('root', 'integer', ['length' => 11, 'unsigned' => true, 'notnull' => false]);
        $table->addColumn('content', 'text', ['notnull' => false]);
        $table->addColumn('content_short', 'text', ['notnull' => false]);
        $table->addColumn('image', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('view', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('view_children', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('sorting', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('is_index', 'smallint', ['length' => 1, 'default' => 0]);
        $table->addColumn('is_published', 'smallint', ['length' => 1, 'default' => 0]);
        $table->addColumn('created_at', 'datetime', ['notnull' => true]);
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->addColumn('published_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['url'], 'url_uniq');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(Page::tableName());
    }
}
