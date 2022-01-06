<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211220210107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id UUID NOT NULL, label VARCHAR(255) NOT NULL, year VARCHAR(4) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN event.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE events_recipients (event_id UUID NOT NULL, recipient_id UUID NOT NULL, PRIMARY KEY(event_id, recipient_id))');
        $this->addSql('CREATE INDEX IDX_C19F874571F7E88B ON events_recipients (event_id)');
        $this->addSql('CREATE INDEX IDX_C19F8745E92F8F78 ON events_recipients (recipient_id)');
        $this->addSql('COMMENT ON COLUMN events_recipients.event_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN events_recipients.recipient_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE events_recipients ADD CONSTRAINT FK_C19F874571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE events_recipients ADD CONSTRAINT FK_C19F8745E92F8F78 FOREIGN KEY (recipient_id) REFERENCES recipient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gift ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE gift ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE gift ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE gift ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE idea ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE idea ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE idea ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE idea ALTER updated_at DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE events_recipients DROP CONSTRAINT FK_C19F874571F7E88B');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE events_recipients');
        $this->addSql('ALTER TABLE gift ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE gift ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE gift ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE gift ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE idea ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE idea ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE idea ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE idea ALTER updated_at DROP DEFAULT');
    }
}
