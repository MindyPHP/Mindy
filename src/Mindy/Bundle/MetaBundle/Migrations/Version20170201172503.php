<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MetaBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Mindy\Bundle\MetaBundle\Model\Meta;

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
        $table = $schema->createTable(Meta::tableName());
        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('host', 'string', ['length' => 255]);
        $table->addColumn('title', 'string', ['length' => 60]);
        $table->addColumn('url', 'string', ['length' => 255]);
        $table->addColumn('keywords', 'string', ['length' => 60, 'notnull' => false]);
        $table->addColumn('canonical', 'string', ['length' => 60, 'notnull' => false]);
        $table->addColumn('description', 'string', ['length' => 160, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['url'], 'url_uniq');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(Meta::tableName());
    }
}
