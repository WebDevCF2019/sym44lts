<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216085615 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, themail VARCHAR(255) NOT NULL, thename VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_AD8A54A9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article CHANGE user_iduser user_iduser INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE categ_has_article DROP FOREIGN KEY fk_categ_has_article_article1');
        $this->addSql('ALTER TABLE categ_has_article DROP FOREIGN KEY fk_categ_has_article_categ1');
        $this->addSql('ALTER TABLE categ_has_article ADD CONSTRAINT FK_C9C471808A6C4123 FOREIGN KEY (categ_idcateg) REFERENCES categ (idcateg)');
        $this->addSql('ALTER TABLE categ_has_article ADD CONSTRAINT FK_C9C4718070B0C2C FOREIGN KEY (article_idarticle) REFERENCES article (idarticle)');
        $this->addSql('ALTER TABLE categ_has_article RENAME INDEX fk_categ_has_article_categ1_idx TO IDX_C9C471808A6C4123');
        $this->addSql('ALTER TABLE categ_has_article RENAME INDEX fk_categ_has_article_article1_idx TO IDX_C9C4718070B0C2C');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE admin_user');
        $this->addSql('ALTER TABLE article CHANGE user_iduser user_iduser INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE categ_has_article DROP FOREIGN KEY FK_C9C471808A6C4123');
        $this->addSql('ALTER TABLE categ_has_article DROP FOREIGN KEY FK_C9C4718070B0C2C');
        $this->addSql('ALTER TABLE categ_has_article ADD CONSTRAINT fk_categ_has_article_article1 FOREIGN KEY (article_idarticle) REFERENCES article (idarticle) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categ_has_article ADD CONSTRAINT fk_categ_has_article_categ1 FOREIGN KEY (categ_idcateg) REFERENCES categ (idcateg) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categ_has_article RENAME INDEX idx_c9c4718070b0c2c TO fk_categ_has_article_article1_idx');
        $this->addSql('ALTER TABLE categ_has_article RENAME INDEX idx_c9c471808a6c4123 TO fk_categ_has_article_categ1_idx');
    }
}
