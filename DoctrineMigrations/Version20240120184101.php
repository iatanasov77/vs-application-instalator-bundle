<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240120184101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('TRUNCATE TABLE VSAPP_Widgets');

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSAPP_WidgetGroups (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A6E1C666DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_WidgetsConfigs (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, config LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_9FA9B94B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSAPP_WidgetGroups ADD CONSTRAINT FK_A6E1C666DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSAPP_WidgetsConfigs ADD CONSTRAINT FK_9FA9B94B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSUM_Users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSAPP_Widgets DROP FOREIGN KEY FK_72F1C48A7E3C61F9');
        $this->addSql('DROP INDEX UNIQ_72F1C48A7E3C61F9 ON VSAPP_Widgets');
        $this->addSql('ALTER TABLE VSAPP_Widgets ADD group_id INT NOT NULL, ADD code VARCHAR(255) NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD active TINYINT(1) DEFAULT 1 NOT NULL, DROP owner_id, DROP config');
        $this->addSql('ALTER TABLE VSAPP_Widgets ADD CONSTRAINT FK_72F1C48AFE54D947 FOREIGN KEY (group_id) REFERENCES VSAPP_WidgetGroups (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72F1C48A77153098 ON VSAPP_Widgets (code)');
        $this->addSql('CREATE INDEX IDX_72F1C48AFE54D947 ON VSAPP_Widgets (group_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSAPP_Widgets DROP FOREIGN KEY FK_72F1C48AFE54D947');
        $this->addSql('ALTER TABLE VSAPP_WidgetGroups DROP FOREIGN KEY FK_A6E1C666DE13F470');
        $this->addSql('ALTER TABLE VSAPP_WidgetsConfigs DROP FOREIGN KEY FK_9FA9B94B7E3C61F9');
        $this->addSql('DROP TABLE VSAPP_WidgetGroups');
        $this->addSql('DROP TABLE VSAPP_WidgetsConfigs');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('DROP INDEX UNIQ_72F1C48A77153098 ON VSAPP_Widgets');
        $this->addSql('DROP INDEX IDX_72F1C48AFE54D947 ON VSAPP_Widgets');
        $this->addSql('ALTER TABLE VSAPP_Widgets ADD owner_id INT DEFAULT NULL, ADD config LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP group_id, DROP code, DROP name, DROP description, DROP active');
        $this->addSql('ALTER TABLE VSAPP_Widgets ADD CONSTRAINT FK_72F1C48A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSUM_Users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72F1C48A7E3C61F9 ON VSAPP_Widgets (owner_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
    }
}
