<?php

namespace Oro\Bundle\ApruveBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroApruveBundleInstaller implements Installation
{
    #[\Override]
    public function getMigrationVersion(): string
    {
        return 'v1_0';
    }

    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->updateOroIntegrationTransportTable($schema);

        /** Tables generation **/
        $this->createOroApruveShortLabelTable($schema);
        $this->createOroApruveTransLabelTable($schema);

        /** Foreign keys generation **/
        $this->addOroApruveShortLabelForeignKeys($schema);
        $this->addOroApruveTransLabelForeignKeys($schema);
    }

    private function updateOroIntegrationTransportTable(Schema $schema): void
    {
        $table = $schema->getTable('oro_integration_transport');
        $table->addColumn('apruve_test_mode', 'boolean', ['notnull' => false, 'default' => false]);
        $table->addColumn('apruve_merchant_id', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('apruve_api_key', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('apruve_webhook_token', 'string', ['notnull' => false, 'length' => 255]);
    }

    /**
     * Create oro_apruve_short_label table
     */
    private function createOroApruveShortLabelTable(Schema $schema): void
    {
        $table = $schema->createTable('oro_apruve_short_label');
        $table->addColumn('transport_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'UNIQ_1FAEF591EB576E89');
        $table->addIndex(['transport_id'], 'IDX_1FAEF5919909C13F');
    }

    /**
     * Create oro_apruve_trans_label table
     */
    private function createOroApruveTransLabelTable(Schema $schema): void
    {
        $table = $schema->createTable('oro_apruve_trans_label');
        $table->addColumn('transport_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'UNIQ_2068304BEB576E89');
    }

    /**
     * Add oro_apruve_short_label foreign keys.
     */
    private function addOroApruveShortLabelForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('oro_apruve_short_label');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Add oro_apruve_trans_label foreign keys.
     */
    private function addOroApruveTransLabelForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('oro_apruve_trans_label');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
