<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210705031111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD762596F6');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('DROP INDEX IDX_4A491FD762596F6 ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD site_id  INT DEFAULT NULL, ADD maintenance_page_id  INT DEFAULT NULL, DROP site_id, DROP maintenance_page_id');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD762596F6 FOREIGN KEY (site_id ) REFERENCES VSAPP_Sites (id)');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('CREATE INDEX IDX_4A491FD762596F6 ON VSAPP_Settings (site_id )');
        $this->addSql('ALTER TABLE VSCMS_MultiPageToc DROP FOREIGN KEY FK_69A01BB5B4CE9742');
        $this->addSql('ALTER TABLE VSCMS_MultiPageToc ADD CONSTRAINT FK_69A01BB5B4CE9742 FOREIGN KEY (toc_root_page_id) REFERENCES VSCMS_TocPage (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_Pages DROP FOREIGN KEY FK_345A075AE24B3F6');
        $this->addSql('DROP INDEX IDX_345A075AE24B3F6 ON VSCMS_Pages');
        $this->addSql('ALTER TABLE VSCMS_Pages DROP multipage_toc_id, DROP type');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_F8BA64CA727ACA70');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_F8BA64CAA977936C');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_F8BA64CAC4663E4');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_F8BA64CA727ACA70 FOREIGN KEY (parent_id) REFERENCES VSCMS_TocPage (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_F8BA64CAA977936C FOREIGN KEY (tree_root) REFERENCES VSCMS_TocPage (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_F8BA64CAC4663E4 FOREIGN KEY (page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD762596F6');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD762596F6 ON VSAPP_Settings');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD site_id INT DEFAULT NULL, ADD maintenance_page_id INT DEFAULT NULL, DROP site_id , DROP maintenance_page_id ');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD762596F6 FOREIGN KEY (site_id) REFERENCES VSAPP_Sites (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD762596F6 ON VSAPP_Settings (site_id)');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSCMS_MultiPageToc DROP FOREIGN KEY FK_B262621CB4CE9742');
        $this->addSql('DROP INDEX idx_b262621cb4ce9742 ON VSCMS_MultiPageToc');
        $this->addSql('CREATE INDEX IDX_69A01BB5B4CE9742 ON VSCMS_MultiPageToc (toc_root_page_id)');
        $this->addSql('ALTER TABLE VSCMS_MultiPageToc ADD CONSTRAINT FK_B262621CB4CE9742 FOREIGN KEY (toc_root_page_id) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_Pages ADD multipage_toc_id INT DEFAULT NULL, ADD type INT NOT NULL');
        $this->addSql('ALTER TABLE VSCMS_Pages ADD CONSTRAINT FK_345A075AE24B3F6 FOREIGN KEY (multipage_toc_id) REFERENCES VSCMS_MultiPageToc (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_345A075AE24B3F6 ON VSCMS_Pages (multipage_toc_id)');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_6B1FF241C4663E4');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_6B1FF241A977936C');
        $this->addSql('ALTER TABLE VSCMS_TocPage DROP FOREIGN KEY FK_6B1FF241727ACA70');
        $this->addSql('DROP INDEX idx_6b1ff241727aca70 ON VSCMS_TocPage');
        $this->addSql('CREATE INDEX IDX_F8BA64CA727ACA70 ON VSCMS_TocPage (parent_id)');
        $this->addSql('DROP INDEX idx_6b1ff241a977936c ON VSCMS_TocPage');
        $this->addSql('CREATE INDEX IDX_F8BA64CAA977936C ON VSCMS_TocPage (tree_root)');
        $this->addSql('DROP INDEX idx_6b1ff241c4663e4 ON VSCMS_TocPage');
        $this->addSql('CREATE INDEX IDX_F8BA64CAC4663E4 ON VSCMS_TocPage (page_id)');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_6B1FF241C4663E4 FOREIGN KEY (page_id) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_6B1FF241A977936C FOREIGN KEY (tree_root) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSCMS_TocPage ADD CONSTRAINT FK_6B1FF241727ACA70 FOREIGN KEY (parent_id) REFERENCES VSCMS_TocPage (id) ON DELETE CASCADE');
    }
}
