<?php

namespace Mindgruve\Gruver\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160324002221 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, name VARCHAR(140) NOT NULL, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX project_name_constraint ON project (name)');
        $this->addSql('CREATE TABLE "release" (id INTEGER NOT NULL, project_id INTEGER NOT NULL, service_id INTEGER NOT NULL, previous_release_id INTEGER DEFAULT NULL, next_release_id INTEGER DEFAULT NULL, tag VARCHAR(140) NOT NULL, uuid VARCHAR(140) NOT NULL, containerId VARCHAR(140) NOT NULL, containerIp VARCHAR(140) NOT NULL, containerPort VARCHAR(140) NOT NULL, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9E47031D166D1F9C ON "release" (project_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DED5CA9E6 ON "release" (service_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DC62A30C4 ON "release" (previous_release_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DEBD99BE ON "release" (next_release_id)');
        $this->addSql('CREATE UNIQUE INDEX tag_constraint ON "release" (service_id, tag)');
        $this->addSql('CREATE TABLE service (id INTEGER NOT NULL, project_id INTEGER NOT NULL, current_release_id INTEGER DEFAULT NULL, pending_release_id INTEGER DEFAULT NULL, most_recent_release_id INTEGER DEFAULT NULL, rollback_release_id INTEGER DEFAULT NULL, name VARCHAR(140) NOT NULL, publicHosts CLOB NOT NULL, publicPorts CLOB NOT NULL, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD2166D1F9C ON service (project_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD26E91BB0F ON service (current_release_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2CB0BABC2 ON service (pending_release_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD266809B62 ON service (most_recent_release_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2172DB4D2 ON service (rollback_release_id)');
        $this->addSql('CREATE UNIQUE INDEX service_name_constraint ON service (project_id, name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE "release"');
        $this->addSql('DROP TABLE service');
    }
}
