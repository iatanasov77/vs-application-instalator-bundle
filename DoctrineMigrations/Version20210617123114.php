<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210617123114 extends AbstractMigration
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
        $this->addSql('ALTER TABLE VSAPP_Taxonomy ADD code VARCHAR(255) NOT NULL AFTER id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1CF3890577153098 ON VSAPP_Taxonomy (code)');
        $this->addSql('ALTER TABLE VSAPP_Taxons CHANGE position position INT DEFAULT NULL');
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
        $this->addSql('DROP INDEX uniq_1cf3890577153098 ON VSAPP_Taxonomy');
        $this->addSql('ALTER TABLE VSAPP_Taxonomy DROP code');
        $this->addSql('ALTER TABLE VSAPP_Taxons CHANGE position position INT NOT NULL');
    }
}
