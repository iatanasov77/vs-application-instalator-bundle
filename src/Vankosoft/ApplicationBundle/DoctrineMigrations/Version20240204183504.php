<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204183504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSAPP_WidgetsRegistry (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, config LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_93AB24C57E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSAPP_WidgetsRegistry ADD CONSTRAINT FK_93AB24C57E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSUM_Users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_WidgetsConfigs DROP FOREIGN KEY FK_9FA9B94B7E3C61F9');
        $this->addSql('DROP TABLE VSAPP_WidgetsConfigs');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSAPP_WidgetsConfigs (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, config LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_9FA9B94B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE VSAPP_WidgetsConfigs ADD CONSTRAINT FK_9FA9B94B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSUM_Users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_WidgetsRegistry DROP FOREIGN KEY FK_93AB24C57E3C61F9');
        $this->addSql('DROP TABLE VSAPP_WidgetsRegistry');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
    }
}
