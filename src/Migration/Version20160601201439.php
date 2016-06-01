<?php

namespace Mindgruve\Gruver\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160601201439 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_9E47031D166D1F9C');
        $this->addSql('DROP INDEX IDX_9E47031DED5CA9E6');
        $this->addSql('DROP INDEX IDX_9E47031DC62A30C4');
        $this->addSql('DROP INDEX IDX_9E47031DEBD99BE');
        $this->addSql('DROP INDEX tag_constraint');
        $this->addSql('CREATE TEMPORARY TABLE __temp__release AS SELECT id, project_id, service_id, previous_release_id, next_release_id, tag, uuid, containerId, containerIp, containerPort, status, created_at, modified_at FROM "release"');
        $this->addSql('DROP TABLE "release"');
        $this->addSql('CREATE TABLE "release" (id INTEGER NOT NULL, project_id INTEGER NOT NULL, service_id INTEGER NOT NULL, previous_release_id INTEGER DEFAULT NULL, next_release_id INTEGER DEFAULT NULL, tag VARCHAR(140) NOT NULL COLLATE BINARY, uuid VARCHAR(140) NOT NULL COLLATE BINARY, containerId VARCHAR(140) NOT NULL COLLATE BINARY, containerIp VARCHAR(140) NOT NULL COLLATE BINARY, containerPort VARCHAR(140) NOT NULL COLLATE BINARY, status VARCHAR(10) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_9E47031D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E47031DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E47031DC62A30C4 FOREIGN KEY (previous_release_id) REFERENCES "release" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E47031DEBD99BE FOREIGN KEY (next_release_id) REFERENCES "release" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "release" (id, project_id, service_id, previous_release_id, next_release_id, tag, uuid, containerId, containerIp, containerPort, status, created_at, modified_at) SELECT id, project_id, service_id, previous_release_id, next_release_id, tag, uuid, containerId, containerIp, containerPort, status, created_at, modified_at FROM __temp__release');
        $this->addSql('DROP TABLE __temp__release');
        $this->addSql('CREATE INDEX IDX_9E47031D166D1F9C ON "release" (project_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DED5CA9E6 ON "release" (service_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DC62A30C4 ON "release" (previous_release_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DEBD99BE ON "release" (next_release_id)');
        $this->addSql('CREATE UNIQUE INDEX tag_constraint ON "release" (service_id, tag)');
        $this->addSql('DROP INDEX IDX_E19D9AD2166D1F9C');
        $this->addSql('DROP INDEX UNIQ_E19D9AD26E91BB0F');
        $this->addSql('DROP INDEX UNIQ_E19D9AD2CB0BABC2');
        $this->addSql('DROP INDEX UNIQ_E19D9AD266809B62');
        $this->addSql('DROP INDEX UNIQ_E19D9AD2172DB4D2');
        $this->addSql('DROP INDEX service_name_constraint');
        $this->addSql('CREATE TEMPORARY TABLE __temp__service AS SELECT id, project_id, current_release_id, pending_release_id, most_recent_release_id, rollback_release_id, name, publicHosts, publicPorts, status, created_at, modified_at FROM service');
        $this->addSql('DROP TABLE service');
        $this->addSql('CREATE TABLE service (id INTEGER NOT NULL, project_id INTEGER NOT NULL, current_release_id INTEGER DEFAULT NULL, pending_release_id INTEGER DEFAULT NULL, most_recent_release_id INTEGER DEFAULT NULL, rollback_release_id INTEGER DEFAULT NULL, name VARCHAR(140) NOT NULL COLLATE BINARY, publicHosts CLOB NOT NULL, publicPorts CLOB NOT NULL, status VARCHAR(10) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, haproxyBackend VARCHAR(1024) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_E19D9AD2166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E19D9AD26E91BB0F FOREIGN KEY (current_release_id) REFERENCES "release" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E19D9AD2CB0BABC2 FOREIGN KEY (pending_release_id) REFERENCES "release" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E19D9AD266809B62 FOREIGN KEY (most_recent_release_id) REFERENCES "release" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E19D9AD2172DB4D2 FOREIGN KEY (rollback_release_id) REFERENCES "release" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO service (id, project_id, current_release_id, pending_release_id, most_recent_release_id, rollback_release_id, name, publicHosts, publicPorts, status, created_at, modified_at) SELECT id, project_id, current_release_id, pending_release_id, most_recent_release_id, rollback_release_id, name, publicHosts, publicPorts, status, created_at, modified_at FROM __temp__service');
        $this->addSql('DROP TABLE __temp__service');
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

        $this->addSql('DROP INDEX IDX_9E47031D166D1F9C');
        $this->addSql('DROP INDEX IDX_9E47031DED5CA9E6');
        $this->addSql('DROP INDEX IDX_9E47031DC62A30C4');
        $this->addSql('DROP INDEX IDX_9E47031DEBD99BE');
        $this->addSql('DROP INDEX tag_constraint');
        $this->addSql('CREATE TEMPORARY TABLE __temp__release AS SELECT id, project_id, service_id, previous_release_id, next_release_id, tag, uuid, containerId, containerIp, containerPort, status, created_at, modified_at FROM "release"');
        $this->addSql('DROP TABLE "release"');
        $this->addSql('CREATE TABLE "release" (id INTEGER NOT NULL, project_id INTEGER NOT NULL, service_id INTEGER NOT NULL, previous_release_id INTEGER DEFAULT NULL, next_release_id INTEGER DEFAULT NULL, tag VARCHAR(140) NOT NULL, uuid VARCHAR(140) NOT NULL, containerId VARCHAR(140) NOT NULL, containerIp VARCHAR(140) NOT NULL, containerPort VARCHAR(140) NOT NULL, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO "release" (id, project_id, service_id, previous_release_id, next_release_id, tag, uuid, containerId, containerIp, containerPort, status, created_at, modified_at) SELECT id, project_id, service_id, previous_release_id, next_release_id, tag, uuid, containerId, containerIp, containerPort, status, created_at, modified_at FROM __temp__release');
        $this->addSql('DROP TABLE __temp__release');
        $this->addSql('CREATE INDEX IDX_9E47031D166D1F9C ON "release" (project_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DED5CA9E6 ON "release" (service_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DC62A30C4 ON "release" (previous_release_id)');
        $this->addSql('CREATE INDEX IDX_9E47031DEBD99BE ON "release" (next_release_id)');
        $this->addSql('CREATE UNIQUE INDEX tag_constraint ON "release" (service_id, tag)');
        $this->addSql('DROP INDEX IDX_E19D9AD2166D1F9C');
        $this->addSql('DROP INDEX UNIQ_E19D9AD26E91BB0F');
        $this->addSql('DROP INDEX UNIQ_E19D9AD2CB0BABC2');
        $this->addSql('DROP INDEX UNIQ_E19D9AD266809B62');
        $this->addSql('DROP INDEX UNIQ_E19D9AD2172DB4D2');
        $this->addSql('DROP INDEX service_name_constraint');
        $this->addSql('CREATE TEMPORARY TABLE __temp__service AS SELECT id, project_id, current_release_id, pending_release_id, most_recent_release_id, rollback_release_id, name, publicHosts, publicPorts, status, created_at, modified_at FROM service');
        $this->addSql('DROP TABLE service');
        $this->addSql('CREATE TABLE service (id INTEGER NOT NULL, project_id INTEGER NOT NULL, current_release_id INTEGER DEFAULT NULL, pending_release_id INTEGER DEFAULT NULL, most_recent_release_id INTEGER DEFAULT NULL, rollback_release_id INTEGER DEFAULT NULL, name VARCHAR(140) NOT NULL, publicHosts CLOB NOT NULL COLLATE BINARY, publicPorts CLOB NOT NULL COLLATE BINARY, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO service (id, project_id, current_release_id, pending_release_id, most_recent_release_id, rollback_release_id, name, publicHosts, publicPorts, status, created_at, modified_at) SELECT id, project_id, current_release_id, pending_release_id, most_recent_release_id, rollback_release_id, name, publicHosts, publicPorts, status, created_at, modified_at FROM __temp__service');
        $this->addSql('DROP TABLE __temp__service');
        $this->addSql('CREATE INDEX IDX_E19D9AD2166D1F9C ON service (project_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD26E91BB0F ON service (current_release_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2CB0BABC2 ON service (pending_release_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD266809B62 ON service (most_recent_release_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2172DB4D2 ON service (rollback_release_id)');
        $this->addSql('CREATE UNIQUE INDEX service_name_constraint ON service (project_id, name)');
    }
}
