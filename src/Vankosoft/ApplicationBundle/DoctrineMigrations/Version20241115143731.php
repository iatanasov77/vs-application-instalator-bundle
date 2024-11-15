<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241115143731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSCMS_BannerImages (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', UNIQUE INDEX UNIQ_317C4867E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_BannerPlaces (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_1DA716B9DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Banners (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, published TINYINT(1) NOT NULL, priority INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Banners_Places (banner_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_61CCBD18684EC833 (banner_id), INDEX IDX_61CCBD18DA6A219 (place_id), PRIMARY KEY(banner_id, place_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSCMS_BannerImages ADD CONSTRAINT FK_317C4867E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSCMS_Banners (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_BannerPlaces ADD CONSTRAINT FK_1DA716B9DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places ADD CONSTRAINT FK_61CCBD18684EC833 FOREIGN KEY (banner_id) REFERENCES VSCMS_Banners (id)');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places ADD CONSTRAINT FK_61CCBD18DA6A219 FOREIGN KEY (place_id) REFERENCES VSCMS_BannerPlaces (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSCMS_TocPage CHANGE position position INT DEFAULT NULL, CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSCMS_BannerImages DROP FOREIGN KEY FK_317C4867E3C61F9');
        $this->addSql('ALTER TABLE VSCMS_BannerPlaces DROP FOREIGN KEY FK_1DA716B9DE13F470');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places DROP FOREIGN KEY FK_61CCBD18684EC833');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places DROP FOREIGN KEY FK_61CCBD18DA6A219');
        $this->addSql('DROP TABLE VSCMS_BannerImages');
        $this->addSql('DROP TABLE VSCMS_BannerPlaces');
        $this->addSql('DROP TABLE VSCMS_Banners');
        $this->addSql('DROP TABLE VSCMS_Banners_Places');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSCMS_TocPage CHANGE slug slug VARCHAR(255) DEFAULT NULL, CHANGE position position INT DEFAULT 999999');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
    }
}
