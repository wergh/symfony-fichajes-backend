<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314165009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work_entry_logs (id INT AUTO_INCREMENT NOT NULL, work_entry_id CHAR(36) NOT NULL COMMENT \'(DC2Type:work_entry_id)\', updated_by_user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:user_id)\', start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4FCE139C6F83C31E (work_entry_id), INDEX IDX_4FCE139C2793CC5E (updated_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_entry_logs ADD CONSTRAINT FK_4FCE139C6F83C31E FOREIGN KEY (work_entry_id) REFERENCES work_entries (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_entry_logs ADD CONSTRAINT FK_4FCE139C2793CC5E FOREIGN KEY (updated_by_user_id) REFERENCES users (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_entry_logs DROP FOREIGN KEY FK_4FCE139C6F83C31E');
        $this->addSql('ALTER TABLE work_entry_logs DROP FOREIGN KEY FK_4FCE139C2793CC5E');
        $this->addSql('DROP TABLE work_entry_logs');
    }
}
