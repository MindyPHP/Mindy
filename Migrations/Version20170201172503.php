<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SeoBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Mindy\Bundle\SeoBundle\Model\Seo;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170201172503 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable(Seo::tableName());
        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('host', 'string', ['length' => 255]);
        $table->addColumn('title', 'string', ['length' => 60]);
        $table->addColumn('url', 'string', ['length' => 255]);
        $table->addColumn('keywords', 'string', ['length' => 60, 'notnull' => false]);
        $table->addColumn('canonical', 'string', ['length' => 60, 'notnull' => false]);
        $table->addColumn('description', 'string', ['length' => 160, 'notnull' => false]);
        $table->addColumn('is_auto', 'smallint', ['length' => 1, 'default' => 1]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['url'], 'url_uniq');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(Seo::tableName());
    }
}
