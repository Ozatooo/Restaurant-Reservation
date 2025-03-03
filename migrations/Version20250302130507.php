<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302130507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD table_id INT NOT NULL, DROP `table`');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('CREATE INDEX IDX_42C84955ECFF285C ON reservation (table_id)');
        $this->addSql('ALTER TABLE `table` ADD restaurant_id INT NOT NULL, DROP restaurant');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_F6298F46B1E7706E ON `table` (restaurant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955ECFF285C');
        $this->addSql('DROP INDEX IDX_42C84955ECFF285C ON reservation');
        $this->addSql('ALTER TABLE reservation ADD `table` VARCHAR(255) NOT NULL, DROP table_id');
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F46B1E7706E');
        $this->addSql('DROP INDEX IDX_F6298F46B1E7706E ON `table`');
        $this->addSql('ALTER TABLE `table` ADD restaurant VARCHAR(255) NOT NULL, DROP restaurant_id');
    }
}
