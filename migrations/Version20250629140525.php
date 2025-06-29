<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250629140525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, musical_genre VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE festival (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, price NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE festival_artist (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, artist_id INT NOT NULL, date DATE NOT NULL, time_slot TIME NOT NULL, stage VARCHAR(50) NOT NULL, INDEX IDX_E68F0A788AEBAF57 (festival_id), INDEX IDX_E68F0A78B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, festival_id INT NOT NULL, INDEX IDX_6117D13BA76ED395 (user_id), INDEX IDX_6117D13B8AEBAF57 (festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_details (user_id_id INT NOT NULL, name VARCHAR(100) NOT NULL, age INT NOT NULL, is_student TINYINT(1) NOT NULL, PRIMARY KEY(user_id_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE festival_artist ADD CONSTRAINT FK_E68F0A788AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE festival_artist ADD CONSTRAINT FK_E68F0A78B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_details ADD CONSTRAINT FK_2A2B15809D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE festival_artist DROP FOREIGN KEY FK_E68F0A788AEBAF57
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE festival_artist DROP FOREIGN KEY FK_E68F0A78B7970CF8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B8AEBAF57
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_details DROP FOREIGN KEY FK_2A2B15809D86650F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE artist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE festival
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE festival_artist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE purchase
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_details
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
