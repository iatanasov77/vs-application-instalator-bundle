<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211107182831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSUM_UserRoles (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, role VARCHAR(32) NOT NULL, INDEX IDX_7F8AAD7E727ACA70 (parent_id), INDEX IDX_7F8AAD7EDE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_Users_Roles (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_82E26E77A76ED395 (user_id), INDEX IDX_82E26E77D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSUM_UserRoles ADD CONSTRAINT FK_7F8AAD7E727ACA70 FOREIGN KEY (parent_id) REFERENCES VSUM_UserRoles (id)');
        $this->addSql('ALTER TABLE VSUM_UserRoles ADD CONSTRAINT FK_7F8AAD7EDE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSUM_Users_Roles ADD CONSTRAINT FK_82E26E77A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUM_Users_Roles ADD CONSTRAINT FK_82E26E77D60322AC FOREIGN KEY (role_id) REFERENCES VSUM_UserRoles (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSUM_Users CHANGE roles roles_array LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSUM_UserRoles DROP FOREIGN KEY FK_7F8AAD7E727ACA70');
        $this->addSql('ALTER TABLE VSUM_Users_Roles DROP FOREIGN KEY FK_82E26E77D60322AC');
        $this->addSql('DROP TABLE VSUM_UserRoles');
        $this->addSql('DROP TABLE VSUM_Users_Roles');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSUM_Users CHANGE roles_array roles LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\'');
    }
}
