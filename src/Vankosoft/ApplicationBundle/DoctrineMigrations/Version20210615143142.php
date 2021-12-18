<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210615143142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSAPP_Locale (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(12) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3DB0A7DB77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_LogEntries (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, INDEX versions_lookup_idx (object_class, object_id), UNIQUE INDEX versions_lookup_unique_idx (object_class, object_id, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Settings (id INT AUTO_INCREMENT NOT NULL, site_id  INT DEFAULT NULL, maintenance_page_id  INT DEFAULT NULL, maintenanceMode TINYINT(1) NOT NULL, theme VARCHAR(255) DEFAULT NULL, INDEX IDX_4A491FD762596F6 (site_id ), INDEX IDX_4A491FD507FAB6A (maintenance_page_id ), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Sites (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_TaxonTranslations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, locale VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_AFE16CB02C2AC5D3 (translatable_id), UNIQUE INDEX slug_uidx (locale, slug), UNIQUE INDEX VSAPP_TaxonTranslations_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Taxonomy (id INT AUTO_INCREMENT NOT NULL, root_taxon_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_1CF38905A54E9E96 (root_taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Taxons (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, tree_left INT NOT NULL, tree_right INT NOT NULL, tree_level INT NOT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2661B30B77153098 (code), INDEX IDX_2661B30BA977936C (tree_root), INDEX IDX_2661B30B727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_PageCategories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, INDEX IDX_98A43648727ACA70 (parent_id), INDEX IDX_98A43648DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Pages (id INT AUTO_INCREMENT NOT NULL, published TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_345A075A989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Pages_Categories (page_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_88D3BD76C4663E4 (page_id), INDEX IDX_88D3BD7612469DE2 (category_id), PRIMARY KEY(page_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_ResetPasswordRequests (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(24) NOT NULL, hashedToken VARCHAR(128) NOT NULL, requestedAt DATETIME NOT NULL, expiresAt DATETIME NOT NULL, INDEX IDX_D6C66D0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_Users (id INT AUTO_INCREMENT NOT NULL, info_id INT DEFAULT NULL, api_token VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, prefered_locale VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, verified TINYINT(1) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_CAFDCD035D8BC1F8 (info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_UsersActivities (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, activity VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_54103277A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_UsersInfo (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, occupation VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3ADA80CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_UsersNotifications (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, notification VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_8D75FA15A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD762596F6 FOREIGN KEY (site_id ) REFERENCES VSAPP_Sites (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_TaxonTranslations ADD CONSTRAINT FK_AFE16CB02C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSAPP_Taxonomy ADD CONSTRAINT FK_1CF38905A54E9E96 FOREIGN KEY (root_taxon_id) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Taxons ADD CONSTRAINT FK_2661B30BA977936C FOREIGN KEY (tree_root) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Taxons ADD CONSTRAINT FK_2661B30B727ACA70 FOREIGN KEY (parent_id) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_PageCategories ADD CONSTRAINT FK_98A43648727ACA70 FOREIGN KEY (parent_id) REFERENCES VSCMS_PageCategories (id)');
        $this->addSql('ALTER TABLE VSCMS_PageCategories ADD CONSTRAINT FK_98A43648DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories ADD CONSTRAINT FK_88D3BD76C4663E4 FOREIGN KEY (page_id) REFERENCES VSCMS_Pages (id)');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories ADD CONSTRAINT FK_88D3BD7612469DE2 FOREIGN KEY (category_id) REFERENCES VSCMS_PageCategories (id)');
        $this->addSql('ALTER TABLE VSUM_ResetPasswordRequests ADD CONSTRAINT FK_D6C66D0A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUM_Users ADD CONSTRAINT FK_CAFDCD035D8BC1F8 FOREIGN KEY (info_id) REFERENCES VSUM_UsersInfo (id)');
        $this->addSql('ALTER TABLE VSUM_UsersActivities ADD CONSTRAINT FK_54103277A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo ADD CONSTRAINT FK_3ADA80CAA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUM_UsersNotifications ADD CONSTRAINT FK_8D75FA15A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD762596F6');
        $this->addSql('ALTER TABLE VSAPP_TaxonTranslations DROP FOREIGN KEY FK_AFE16CB02C2AC5D3');
        $this->addSql('ALTER TABLE VSAPP_Taxonomy DROP FOREIGN KEY FK_1CF38905A54E9E96');
        $this->addSql('ALTER TABLE VSAPP_Taxons DROP FOREIGN KEY FK_2661B30BA977936C');
        $this->addSql('ALTER TABLE VSAPP_Taxons DROP FOREIGN KEY FK_2661B30B727ACA70');
        $this->addSql('ALTER TABLE VSCMS_PageCategories DROP FOREIGN KEY FK_98A43648DE13F470');
        $this->addSql('ALTER TABLE VSCMS_PageCategories DROP FOREIGN KEY FK_98A43648727ACA70');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories DROP FOREIGN KEY FK_88D3BD7612469DE2');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories DROP FOREIGN KEY FK_88D3BD76C4663E4');
        $this->addSql('ALTER TABLE VSUM_ResetPasswordRequests DROP FOREIGN KEY FK_D6C66D0A76ED395');
        $this->addSql('ALTER TABLE VSUM_UsersActivities DROP FOREIGN KEY FK_54103277A76ED395');
        $this->addSql('ALTER TABLE VSUM_UsersInfo DROP FOREIGN KEY FK_3ADA80CAA76ED395');
        $this->addSql('ALTER TABLE VSUM_UsersNotifications DROP FOREIGN KEY FK_8D75FA15A76ED395');
        $this->addSql('ALTER TABLE VSUM_Users DROP FOREIGN KEY FK_CAFDCD035D8BC1F8');
        $this->addSql('DROP TABLE VSAPP_Locale');
        $this->addSql('DROP TABLE VSAPP_LogEntries');
        $this->addSql('DROP TABLE VSAPP_Settings');
        $this->addSql('DROP TABLE VSAPP_Sites');
        $this->addSql('DROP TABLE VSAPP_TaxonTranslations');
        $this->addSql('DROP TABLE VSAPP_Taxonomy');
        $this->addSql('DROP TABLE VSAPP_Taxons');
        $this->addSql('DROP TABLE VSAPP_Translations');
        $this->addSql('DROP TABLE VSCMS_PageCategories');
        $this->addSql('DROP TABLE VSCMS_Pages');
        $this->addSql('DROP TABLE VSCMS_Pages_Categories');
        $this->addSql('DROP TABLE VSUM_ResetPasswordRequests');
        $this->addSql('DROP TABLE VSUM_Users');
        $this->addSql('DROP TABLE VSUM_UsersActivities');
        $this->addSql('DROP TABLE VSUM_UsersInfo');
        $this->addSql('DROP TABLE VSUM_UsersNotifications');
    }
}
