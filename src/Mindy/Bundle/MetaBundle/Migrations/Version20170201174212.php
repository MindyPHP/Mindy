<?php

namespace Mindy\Bundle\MetaBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Mindy\Bundle\MetaBundle\Model\Template;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170201174212 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable(Template::tableName());
        $table->addColumn('id', 'integer', ['unsigned' => true]);
        $table->addColumn('code', 'string', ['length' => 255]);
        $table->addColumn('content', 'text');
        $table->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(Template::tableName());
    }
}
