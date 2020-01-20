<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200120163859 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE manga (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapter (id INT AUTO_INCREMENT NOT NULL, manga_id INT DEFAULT NULL, number INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F981B52E7B6461 (manga_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_alert (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8AB0074EE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_alert_manga (email_alert_id INT NOT NULL, manga_id INT NOT NULL, INDEX IDX_8AC0155F9865403B (email_alert_id), INDEX IDX_8AC0155F7B6461 (manga_id), PRIMARY KEY(email_alert_id, manga_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('ALTER TABLE email_alert_manga ADD CONSTRAINT FK_8AC0155F9865403B FOREIGN KEY (email_alert_id) REFERENCES email_alert (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email_alert_manga ADD CONSTRAINT FK_8AC0155F7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E7B6461');
        $this->addSql('ALTER TABLE email_alert_manga DROP FOREIGN KEY FK_8AC0155F7B6461');
        $this->addSql('ALTER TABLE email_alert_manga DROP FOREIGN KEY FK_8AC0155F9865403B');
        $this->addSql('DROP TABLE manga');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE email_alert');
        $this->addSql('DROP TABLE email_alert_manga');
    }
}
