<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210702150353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSCMS_MultiPageToc (id INT AUTO_INCREMENT NOT NULL, toc_root_page_id INT NOT NULL, toc_title VARCHAR(255) NOT NULL, INDEX IDX_69A01BB5B4CE9742 (toc_root_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSCMS_TocPage (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, lvl INT NOT NULL, INDEX IDX_F8BA64CAC4663E4 (page_id), INDEX IDX_F8BA64CAA977936C (tree_root), INDEX IDX_F8BA64CA727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSCMS_MultiPageToc ADD CONSTRAINT FK_69A01BB5B4CE9742 FOREIGN KEY (toc_root_page_id) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_F8BA64CAC4663E4 FOREIGN KEY (page_id) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_F8BA64CAA977936C FOREIGN KEY (tree_root) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_F8BA64CA727ACA70 FOREIGN KEY (parent_id) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD762596F6');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('DROP INDEX IDX_4A491FD762596F6 ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD site_id  INT DEFAULT NULL, ADD maintenance_page_id  INT DEFAULT NULL, DROP site_id, DROP maintenance_page_id');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD762596F6 FOREIGN KEY (site_id ) REFERENCES VSAPP_Sites (id)');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('CREATE INDEX IDX_4A491FD762596F6 ON VSAPP_Settings (site_id )');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSCMS_MultiPageToc DROP FOREIGN KEY FK_69A01BB5B4CE9742');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_F8BA64CAA977936C');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_F8BA64CA727ACA70');
        $this->addSql('DROP TABLE VSCMS_MultiPageToc');
        $this->addSql('DROP TABLE VSCMS_TocPage');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD762596F6');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD762596F6 ON VSAPP_Settings');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD site_id INT DEFAULT NULL, ADD maintenance_page_id INT DEFAULT NULL, DROP site_id , DROP maintenance_page_id ');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD762596F6 FOREIGN KEY (site_id) REFERENCES VSAPP_Sites (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD762596F6 ON VSAPP_Settings (site_id)');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
    }
}
