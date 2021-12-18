<?php

declare(strict_types=1);

namespace Vankosoft\ApplicationBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207165617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSAPP_Applications_Users (application_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4F97A75D3E030ACD (application_id), INDEX IDX_4F97A75DA76ED395 (user_id), PRIMARY KEY(application_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSAPP_Applications_Users ADD CONSTRAINT FK_4F97A75D3E030ACD FOREIGN KEY (application_id) REFERENCES VSAPP_Applications (id)');
        $this->addSql('ALTER TABLE VSAPP_Applications_Users ADD CONSTRAINT FK_4F97A75DA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7F8AAD7E57698A6A ON VSUM_UserRoles (role)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo DROP FOREIGN KEY FK_3ADA80CAA76ED395');
        $this->addSql('DROP INDEX UNIQ_3ADA80CAA76ED395 ON VSUM_UsersInfo');
        $this->addSql('ALTER TABLE VSUM_UsersInfo DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE VSAPP_Applications_Users');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('DROP INDEX UNIQ_7F8AAD7E57698A6A ON VSUM_UserRoles');
        $this->addSql('ALTER TABLE VSUM_UsersInfo ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSUM_UsersInfo ADD CONSTRAINT FK_3ADA80CAA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3ADA80CAA76ED395 ON VSUM_UsersInfo (user_id)');
    }
}
