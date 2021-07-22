<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210721120424 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C25028243124B5B6 ON app_users');
        $this->addSql('DROP INDEX UNIQ_C250282483A00E68 ON app_users');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C25028243124B5B6 ON app_users (lastname)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C250282483A00E68 ON app_users (firstname)');
    }
}
