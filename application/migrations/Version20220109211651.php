<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220109211651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "group" ADD color_code VARCHAR(31) DEFAULT NULL');

        $this->addSql(
            <<<'SQL'
                UPDATE "group" SET color_code = :colorCode WHERE label = :label
            SQL,
            [
                'label' => 'Famille Moflo',
                'colorCode' => 'amber lighten-1',
            ],
        );
        $this->addSql(
            <<<'SQL'
                UPDATE "group" SET color_code = :colorCode WHERE label = :label
            SQL,
            [
                'label' => 'Famille Abélard',
                'colorCode' => 'red lighten-2',
            ],
        );
        $this->addSql(
            <<<'SQL'
                UPDATE "group" SET color_code = :colorCode WHERE label = :label
            SQL,
            [
                'label' => 'Famille Lasterre',
                'colorCode' => 'light-blue lighten-3',
            ],
        );
        $this->addSql(
            <<<'SQL'
                UPDATE "group" SET color_code = :colorCode WHERE label = :label
            SQL,
            [
                'label' => 'Famille Verlaguet',
                'colorCode' => 'green lighten-2',
            ],
        );
        $this->addSql(
            <<<'SQL'
                UPDATE "group" SET color_code = :colorCode WHERE label = :label
            SQL,
            [
                'label' => 'Amis',
                'colorCode' => 'blue-grey lighten-2',
            ],
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "group" DROP color_code');
    }
}
