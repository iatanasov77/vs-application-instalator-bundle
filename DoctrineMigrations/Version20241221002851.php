<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221002851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSAPP_Applications (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, hostname VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7797295A77153098 (code), INDEX IDX_7797295AE551C011 (hostname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Applications_Users (application_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4F97A75D3E030ACD (application_id), INDEX IDX_4F97A75DA76ED395 (user_id), PRIMARY KEY(application_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_CookieConsentTranslations (id INT AUTO_INCREMENT NOT NULL, language_code VARCHAR(8) NOT NULL, locale_code VARCHAR(8) DEFAULT \'en_US\' NOT NULL COMMENT \'From wich of Locales is this Language Created.\', btn_accept_all VARCHAR(64) NOT NULL, btn_reject_all VARCHAR(64) NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, label VARCHAR(64) DEFAULT \'Cookie Consent\' NOT NULL, btn_accept_necessary VARCHAR(64) DEFAULT \'Accept necessary\' NOT NULL, btn_show_preferences VARCHAR(64) DEFAULT \'Manage individual preferences\' NOT NULL, UNIQUE INDEX UNIQ_F761D537451CDAD4 (language_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_InstalationInfo (id INT AUTO_INCREMENT NOT NULL, version VARCHAR(12) NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Locale (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) DEFAULT NULL, code VARCHAR(12) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3DB0A7DB77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_LogEntries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, locale VARCHAR(8) NOT NULL, INDEX versions_lookup_idx (object_class, object_id), UNIQUE INDEX versions_lookup_unique_idx (object_class, object_id, version, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Settings (id INT AUTO_INCREMENT NOT NULL, application_id INT DEFAULT NULL, maintenance_page_id  INT DEFAULT NULL, maintenanceMode TINYINT(1) DEFAULT 0 NOT NULL COMMENT \'This Application is In Maintenace Mode.\', theme VARCHAR(255) DEFAULT NULL, INDEX IDX_4A491FD3E030ACD (application_id), INDEX IDX_4A491FD507FAB6A (maintenance_page_id ), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_TagsWhitelistContexts (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_AD13B59BDE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_TagsWhitelistTags (id INT AUTO_INCREMENT NOT NULL, context_id INT DEFAULT NULL, tag VARCHAR(32) NOT NULL, INDEX IDX_CC268F106B00C1CF (context_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_TaxonImage (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', INDEX IDX_E31A81167E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_TaxonTranslations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, locale VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_AFE16CB02C2AC5D3 (translatable_id), UNIQUE INDEX slug_uidx (locale, slug), UNIQUE INDEX VSAPP_TaxonTranslations_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Taxonomy (id INT AUTO_INCREMENT NOT NULL, root_taxon_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1CF3890577153098 (code), UNIQUE INDEX UNIQ_1CF38905A54E9E96 (root_taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Taxons (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, tree_left INT NOT NULL, tree_right INT NOT NULL, tree_level INT NOT NULL, position INT DEFAULT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2661B30B77153098 (code), INDEX IDX_2661B30BA977936C (tree_root), INDEX IDX_2661B30B727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_WidgetGroups (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A6E1C666DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Widgets (id INT AUTO_INCREMENT NOT NULL, group_id INT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, allow_anonymous TINYINT(1) DEFAULT 0 NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX UNIQ_72F1C48A77153098 (code), INDEX IDX_72F1C48AFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_Widgets_Roles (widget_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_37D65906FBE885E2 (widget_id), INDEX IDX_37D65906D60322AC (role_id), PRIMARY KEY(widget_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSAPP_WidgetsRegistry (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, config LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_93AB24C57E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_BannerImages (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', UNIQUE INDEX UNIQ_317C4867E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_BannerPlaces (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, imagine_filter VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1DA716B9DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Banners (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, published TINYINT(1) NOT NULL, priority INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Banners_Places (banner_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_61CCBD18684EC833 (banner_id), INDEX IDX_61CCBD18DA6A219 (place_id), PRIMARY KEY(banner_id, place_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_DocumentCategories (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E0054AF0DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Documents (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, toc_root_page_id INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_E15A147F12469DE2 (category_id), UNIQUE INDEX UNIQ_E15A147FB4CE9742 (toc_root_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_FileManager (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8B912DE4DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_FileManagerFile (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', INDEX IDX_57157B017E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_HelpCenterQuestions (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(255) NOT NULL, answer LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_PageCategories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, INDEX IDX_98A43648727ACA70 (parent_id), UNIQUE INDEX UNIQ_98A43648DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Pages (id INT AUTO_INCREMENT NOT NULL, published TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT \'\', text LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_345A075A989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Pages_Categories (page_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_88D3BD76C4663E4 (page_id), INDEX IDX_88D3BD7612469DE2 (category_id), PRIMARY KEY(page_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_QuickLinks (id INT AUTO_INCREMENT NOT NULL, published TINYINT(1) NOT NULL, link_text VARCHAR(255) NOT NULL, link_path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_Sliders (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D9BD32AFDE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_SlidersItems (id INT AUTO_INCREMENT NOT NULL, slider_id INT DEFAULT NULL, active TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_15F6ED312CCC9638 (slider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_SlidersItemsPhotos (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', UNIQUE INDEX UNIQ_A9581A697E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_TocPage (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, tree_left INT NOT NULL, tree_right INT NOT NULL, tree_level INT NOT NULL, UNIQUE INDEX UNIQ_6B1FF241989D9B62 (slug), INDEX IDX_6B1FF241A977936C (tree_root), INDEX IDX_6B1FF241727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_AvatarImage (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT \'\' NOT NULL COMMENT \'The Original Name of the File.\', UNIQUE INDEX UNIQ_D917FB667E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_ResetPasswordRequests (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(255) NOT NULL, hashedToken VARCHAR(255) NOT NULL, requestedAt DATETIME NOT NULL, expiresAt DATETIME NOT NULL, INDEX IDX_D6C66D0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_UserRoles (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7F8AAD7E57698A6A (role), INDEX IDX_7F8AAD7E727ACA70 (parent_id), UNIQUE INDEX UNIQ_7F8AAD7EDE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_Users (id INT AUTO_INCREMENT NOT NULL, info_id INT DEFAULT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles_array LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, prefered_locale VARCHAR(255) DEFAULT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, verified TINYINT(1) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_CAFDCD035D8BC1F8 (info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_Users_Roles (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_82E26E77A76ED395 (user_id), INDEX IDX_82E26E77D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_UsersActivities (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, activity VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_54103277A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_UsersInfo (id INT AUTO_INCREMENT NOT NULL, title ENUM(\'mr\', \'mrs\', \'miss\'), first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, designation VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, occupation VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUM_UsersNotifications (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, notification_from VARCHAR(255) NOT NULL, notification VARCHAR(255) NOT NULL, notification_body LONGTEXT DEFAULT NULL, readed TINYINT(1) DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_8D75FA15A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSAPP_Applications_Users ADD CONSTRAINT FK_4F97A75D3E030ACD FOREIGN KEY (application_id) REFERENCES VSAPP_Applications (id)');
        $this->addSql('ALTER TABLE VSAPP_Applications_Users ADD CONSTRAINT FK_4F97A75DA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD3E030ACD FOREIGN KEY (application_id) REFERENCES VSAPP_Applications (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_TagsWhitelistContexts ADD CONSTRAINT FK_AD13B59BDE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSAPP_TagsWhitelistTags ADD CONSTRAINT FK_CC268F106B00C1CF FOREIGN KEY (context_id) REFERENCES VSAPP_TagsWhitelistContexts (id)');
        $this->addSql('ALTER TABLE VSAPP_TaxonImage ADD CONSTRAINT FK_E31A81167E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_TaxonTranslations ADD CONSTRAINT FK_AFE16CB02C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Taxonomy ADD CONSTRAINT FK_1CF38905A54E9E96 FOREIGN KEY (root_taxon_id) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Taxons ADD CONSTRAINT FK_2661B30BA977936C FOREIGN KEY (tree_root) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Taxons ADD CONSTRAINT FK_2661B30B727ACA70 FOREIGN KEY (parent_id) REFERENCES VSAPP_Taxons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_WidgetGroups ADD CONSTRAINT FK_A6E1C666DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSAPP_Widgets ADD CONSTRAINT FK_72F1C48AFE54D947 FOREIGN KEY (group_id) REFERENCES VSAPP_WidgetGroups (id)');
        $this->addSql('ALTER TABLE VSAPP_Widgets_Roles ADD CONSTRAINT FK_37D65906FBE885E2 FOREIGN KEY (widget_id) REFERENCES VSAPP_Widgets (id)');
        $this->addSql('ALTER TABLE VSAPP_Widgets_Roles ADD CONSTRAINT FK_37D65906D60322AC FOREIGN KEY (role_id) REFERENCES VSUM_UserRoles (id)');
        $this->addSql('ALTER TABLE VSAPP_WidgetsRegistry ADD CONSTRAINT FK_93AB24C57E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSUM_Users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_BannerImages ADD CONSTRAINT FK_317C4867E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSCMS_Banners (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_BannerPlaces ADD CONSTRAINT FK_1DA716B9DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places ADD CONSTRAINT FK_61CCBD18684EC833 FOREIGN KEY (banner_id) REFERENCES VSCMS_Banners (id)');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places ADD CONSTRAINT FK_61CCBD18DA6A219 FOREIGN KEY (place_id) REFERENCES VSCMS_BannerPlaces (id)');
        $this->addSql('ALTER TABLE VSCMS_DocumentCategories ADD CONSTRAINT FK_E0054AF0DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSCMS_Documents ADD CONSTRAINT FK_E15A147F12469DE2 FOREIGN KEY (category_id) REFERENCES VSCMS_DocumentCategories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_Documents ADD CONSTRAINT FK_E15A147FB4CE9742 FOREIGN KEY (toc_root_page_id) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_FileManager ADD CONSTRAINT FK_8B912DE4DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSCMS_FileManagerFile ADD CONSTRAINT FK_57157B017E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSCMS_FileManager (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_PageCategories ADD CONSTRAINT FK_98A43648727ACA70 FOREIGN KEY (parent_id) REFERENCES VSCMS_PageCategories (id)');
        $this->addSql('ALTER TABLE VSCMS_PageCategories ADD CONSTRAINT FK_98A43648DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories ADD CONSTRAINT FK_88D3BD76C4663E4 FOREIGN KEY (page_id) REFERENCES VSCMS_Pages (id)');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories ADD CONSTRAINT FK_88D3BD7612469DE2 FOREIGN KEY (category_id) REFERENCES VSCMS_PageCategories (id)');
        $this->addSql('ALTER TABLE VSCMS_Sliders ADD CONSTRAINT FK_D9BD32AFDE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSCMS_SlidersItems ADD CONSTRAINT FK_15F6ED312CCC9638 FOREIGN KEY (slider_id) REFERENCES VSCMS_Sliders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_SlidersItemsPhotos ADD CONSTRAINT FK_A9581A697E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSCMS_SlidersItems (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_6B1FF241A977936C FOREIGN KEY (tree_root) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_6B1FF241727ACA70 FOREIGN KEY (parent_id) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSUM_AvatarImage ADD CONSTRAINT FK_D917FB667E3C61F9 FOREIGN KEY (owner_id) REFERENCES VSUM_UsersInfo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSUM_ResetPasswordRequests ADD CONSTRAINT FK_D6C66D0A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUM_UserRoles ADD CONSTRAINT FK_7F8AAD7E727ACA70 FOREIGN KEY (parent_id) REFERENCES VSUM_UserRoles (id)');
        $this->addSql('ALTER TABLE VSUM_UserRoles ADD CONSTRAINT FK_7F8AAD7EDE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSUM_Users ADD CONSTRAINT FK_CAFDCD035D8BC1F8 FOREIGN KEY (info_id) REFERENCES VSUM_UsersInfo (id)');
        $this->addSql('ALTER TABLE VSUM_Users_Roles ADD CONSTRAINT FK_82E26E77A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUM_Users_Roles ADD CONSTRAINT FK_82E26E77D60322AC FOREIGN KEY (role_id) REFERENCES VSUM_UserRoles (id)');
        $this->addSql('ALTER TABLE VSUM_UsersActivities ADD CONSTRAINT FK_54103277A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUM_UsersNotifications ADD CONSTRAINT FK_8D75FA15A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSAPP_Applications_Users DROP FOREIGN KEY FK_4F97A75D3E030ACD');
        $this->addSql('ALTER TABLE VSAPP_Applications_Users DROP FOREIGN KEY FK_4F97A75DA76ED395');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD3E030ACD');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('ALTER TABLE VSAPP_TagsWhitelistContexts DROP FOREIGN KEY FK_AD13B59BDE13F470');
        $this->addSql('ALTER TABLE VSAPP_TagsWhitelistTags DROP FOREIGN KEY FK_CC268F106B00C1CF');
        $this->addSql('ALTER TABLE VSAPP_TaxonImage DROP FOREIGN KEY FK_E31A81167E3C61F9');
        $this->addSql('ALTER TABLE VSAPP_TaxonTranslations DROP FOREIGN KEY FK_AFE16CB02C2AC5D3');
        $this->addSql('ALTER TABLE VSAPP_Taxonomy DROP FOREIGN KEY FK_1CF38905A54E9E96');
        $this->addSql('ALTER TABLE VSAPP_Taxons DROP FOREIGN KEY FK_2661B30BA977936C');
        $this->addSql('ALTER TABLE VSAPP_Taxons DROP FOREIGN KEY FK_2661B30B727ACA70');
        $this->addSql('ALTER TABLE VSAPP_WidgetGroups DROP FOREIGN KEY FK_A6E1C666DE13F470');
        $this->addSql('ALTER TABLE VSAPP_Widgets DROP FOREIGN KEY FK_72F1C48AFE54D947');
        $this->addSql('ALTER TABLE VSAPP_Widgets_Roles DROP FOREIGN KEY FK_37D65906FBE885E2');
        $this->addSql('ALTER TABLE VSAPP_Widgets_Roles DROP FOREIGN KEY FK_37D65906D60322AC');
        $this->addSql('ALTER TABLE VSAPP_WidgetsRegistry DROP FOREIGN KEY FK_93AB24C57E3C61F9');
        $this->addSql('ALTER TABLE VSCMS_BannerImages DROP FOREIGN KEY FK_317C4867E3C61F9');
        $this->addSql('ALTER TABLE VSCMS_BannerPlaces DROP FOREIGN KEY FK_1DA716B9DE13F470');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places DROP FOREIGN KEY FK_61CCBD18684EC833');
        $this->addSql('ALTER TABLE VSCMS_Banners_Places DROP FOREIGN KEY FK_61CCBD18DA6A219');
        $this->addSql('ALTER TABLE VSCMS_DocumentCategories DROP FOREIGN KEY FK_E0054AF0DE13F470');
        $this->addSql('ALTER TABLE VSCMS_Documents DROP FOREIGN KEY FK_E15A147F12469DE2');
        $this->addSql('ALTER TABLE VSCMS_Documents DROP FOREIGN KEY FK_E15A147FB4CE9742');
        $this->addSql('ALTER TABLE VSCMS_FileManager DROP FOREIGN KEY FK_8B912DE4DE13F470');
        $this->addSql('ALTER TABLE VSCMS_FileManagerFile DROP FOREIGN KEY FK_57157B017E3C61F9');
        $this->addSql('ALTER TABLE VSCMS_PageCategories DROP FOREIGN KEY FK_98A43648727ACA70');
        $this->addSql('ALTER TABLE VSCMS_PageCategories DROP FOREIGN KEY FK_98A43648DE13F470');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories DROP FOREIGN KEY FK_88D3BD76C4663E4');
        $this->addSql('ALTER TABLE VSCMS_Pages_Categories DROP FOREIGN KEY FK_88D3BD7612469DE2');
        $this->addSql('ALTER TABLE VSCMS_Sliders DROP FOREIGN KEY FK_D9BD32AFDE13F470');
        $this->addSql('ALTER TABLE VSCMS_SlidersItems DROP FOREIGN KEY FK_15F6ED312CCC9638');
        $this->addSql('ALTER TABLE VSCMS_SlidersItemsPhotos DROP FOREIGN KEY FK_A9581A697E3C61F9');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_6B1FF241A977936C');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_6B1FF241727ACA70');
        $this->addSql('ALTER TABLE VSUM_AvatarImage DROP FOREIGN KEY FK_D917FB667E3C61F9');
        $this->addSql('ALTER TABLE VSUM_ResetPasswordRequests DROP FOREIGN KEY FK_D6C66D0A76ED395');
        $this->addSql('ALTER TABLE VSUM_UserRoles DROP FOREIGN KEY FK_7F8AAD7E727ACA70');
        $this->addSql('ALTER TABLE VSUM_UserRoles DROP FOREIGN KEY FK_7F8AAD7EDE13F470');
        $this->addSql('ALTER TABLE VSUM_Users DROP FOREIGN KEY FK_CAFDCD035D8BC1F8');
        $this->addSql('ALTER TABLE VSUM_Users_Roles DROP FOREIGN KEY FK_82E26E77A76ED395');
        $this->addSql('ALTER TABLE VSUM_Users_Roles DROP FOREIGN KEY FK_82E26E77D60322AC');
        $this->addSql('ALTER TABLE VSUM_UsersActivities DROP FOREIGN KEY FK_54103277A76ED395');
        $this->addSql('ALTER TABLE VSUM_UsersNotifications DROP FOREIGN KEY FK_8D75FA15A76ED395');
        $this->addSql('DROP TABLE VSAPP_Applications');
        $this->addSql('DROP TABLE VSAPP_Applications_Users');
        $this->addSql('DROP TABLE VSAPP_CookieConsentTranslations');
        $this->addSql('DROP TABLE VSAPP_InstalationInfo');
        $this->addSql('DROP TABLE VSAPP_Locale');
        $this->addSql('DROP TABLE VSAPP_LogEntries');
        $this->addSql('DROP TABLE VSAPP_Settings');
        $this->addSql('DROP TABLE VSAPP_TagsWhitelistContexts');
        $this->addSql('DROP TABLE VSAPP_TagsWhitelistTags');
        $this->addSql('DROP TABLE VSAPP_TaxonImage');
        $this->addSql('DROP TABLE VSAPP_TaxonTranslations');
        $this->addSql('DROP TABLE VSAPP_Taxonomy');
        $this->addSql('DROP TABLE VSAPP_Taxons');
        $this->addSql('DROP TABLE VSAPP_Translations');
        $this->addSql('DROP TABLE VSAPP_WidgetGroups');
        $this->addSql('DROP TABLE VSAPP_Widgets');
        $this->addSql('DROP TABLE VSAPP_Widgets_Roles');
        $this->addSql('DROP TABLE VSAPP_WidgetsRegistry');
        $this->addSql('DROP TABLE VSCMS_BannerImages');
        $this->addSql('DROP TABLE VSCMS_BannerPlaces');
        $this->addSql('DROP TABLE VSCMS_Banners');
        $this->addSql('DROP TABLE VSCMS_Banners_Places');
        $this->addSql('DROP TABLE VSCMS_DocumentCategories');
        $this->addSql('DROP TABLE VSCMS_Documents');
        $this->addSql('DROP TABLE VSCMS_FileManager');
        $this->addSql('DROP TABLE VSCMS_FileManagerFile');
        $this->addSql('DROP TABLE VSCMS_HelpCenterQuestions');
        $this->addSql('DROP TABLE VSCMS_PageCategories');
        $this->addSql('DROP TABLE VSCMS_Pages');
        $this->addSql('DROP TABLE VSCMS_Pages_Categories');
        $this->addSql('DROP TABLE VSCMS_QuickLinks');
        $this->addSql('DROP TABLE VSCMS_Sliders');
        $this->addSql('DROP TABLE VSCMS_SlidersItems');
        $this->addSql('DROP TABLE VSCMS_SlidersItemsPhotos');
        $this->addSql('DROP TABLE VSCMS_TocPage');
        $this->addSql('DROP TABLE VSUM_AvatarImage');
        $this->addSql('DROP TABLE VSUM_ResetPasswordRequests');
        $this->addSql('DROP TABLE VSUM_UserRoles');
        $this->addSql('DROP TABLE VSUM_Users');
        $this->addSql('DROP TABLE VSUM_Users_Roles');
        $this->addSql('DROP TABLE VSUM_UsersActivities');
        $this->addSql('DROP TABLE VSUM_UsersInfo');
        $this->addSql('DROP TABLE VSUM_UsersNotifications');
    }
}
