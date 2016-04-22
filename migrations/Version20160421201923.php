<?php
namespace Nathejk\Sms\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160421201923 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE message (
            id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
            uts INTEGER UNSIGNED NOT NULL,
            recipient VARCHAR(20) NOT NULL,
            sender VARCHAR(20) NOT NULL,
            body VARCHAR(250) NOT NULL,
            status VARCHAR(20) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
