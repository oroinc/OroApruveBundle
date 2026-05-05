<?php

namespace Oro\Bundle\ApruveBundle\Migrations\Schema\v1_0_1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Converts Apruve encrypted credential columns from VARCHAR(255) to TEXT to fit AES-encrypted values.
 */
class ChangeEncryptedColumnsToText implements Migration
{
    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        $table = $schema->getTable('oro_integration_transport');

        $this->changeColumnToText($table, 'apruve_merchant_id');
        $this->changeColumnToText($table, 'apruve_api_key');
    }

    private function changeColumnToText(Table $table, string $columnName): void
    {
        if ($table->getColumn($columnName)->getType()->getName() === Types::TEXT) {
            return;
        }

        $table->changeColumn($columnName, ['type' => Type::getType(Types::TEXT), 'length' => null]);
    }
}
