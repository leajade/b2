<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201111141335 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE kinds_contracts (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offers (id INT AUTO_INCREMENT NOT NULL, type_contract_id INT NOT NULL, kind_contract_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, address VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, update_date DATETIME NOT NULL, end_contract DATETIME DEFAULT NULL, INDEX IDX_DA4604276E6F376C (type_contract_id), INDEX IDX_DA460427327F41EE (kind_contract_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE types_contracts (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA4604276E6F376C FOREIGN KEY (type_contract_id) REFERENCES types_contracts (id)');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA460427327F41EE FOREIGN KEY (kind_contract_id) REFERENCES kinds_contracts (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY FK_DA460427327F41EE');
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY FK_DA4604276E6F376C');
        $this->addSql('DROP TABLE kinds_contracts');
        $this->addSql('DROP TABLE offers');
        $this->addSql('DROP TABLE types_contracts');
    }
}
