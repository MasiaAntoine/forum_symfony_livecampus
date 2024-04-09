<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240409150857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board DROP FOREIGN KEY FK_58562B479777D11E');
        $this->addSql('ALTER TABLE board DROP FOREIGN KEY FK_58562B4799B8CBC0');
        $this->addSql('DROP INDEX IDX_58562B479777D11E ON board');
        $this->addSql('DROP INDEX IDX_58562B4799B8CBC0 ON board');
        $this->addSql('ALTER TABLE board ADD category_id INT NOT NULL, ADD user_id INT NOT NULL, DROP category_id_id, DROP user_uuid_id');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B4712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_58562B4712469DE2 ON board (category_id)');
        $this->addSql('CREATE INDEX IDX_58562B47A76ED395 ON board (user_id)');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C199B8CBC0');
        $this->addSql('DROP INDEX IDX_64C19C199B8CBC0 ON category');
        $this->addSql('ALTER TABLE category CHANGE user_uuid_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1A76ED395 ON category (user_id)');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361080E261BC');
        $this->addSql('DROP INDEX IDX_8C9F361080E261BC ON file');
        $this->addSql('ALTER TABLE file CHANGE message_id_id message_id INT NOT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610537A1329 ON file (message_id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F99B8CBC0');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F6ED75F8F');
        $this->addSql('DROP INDEX IDX_B6BD307F99B8CBC0 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F6ED75F8F ON message');
        $this->addSql('ALTER TABLE message ADD user_id INT NOT NULL, ADD subject_id INT NOT NULL, DROP subject_id_id, DROP user_uuid_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FA76ED395 ON message (user_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F23EDC87 ON message (subject_id)');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A99B8CBC0');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7ADDF9797C');
        $this->addSql('DROP INDEX IDX_FBCE3E7ADDF9797C ON subject');
        $this->addSql('DROP INDEX IDX_FBCE3E7A99B8CBC0 ON subject');
        $this->addSql('ALTER TABLE subject ADD board_id INT NOT NULL, DROP board_id_id, DROP user_uuid_id');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('CREATE INDEX IDX_FBCE3E7AE7EC5785 ON subject (board_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AE7EC5785');
        $this->addSql('DROP INDEX IDX_FBCE3E7AE7EC5785 ON subject');
        $this->addSql('ALTER TABLE subject ADD user_uuid_id INT NOT NULL, CHANGE board_id board_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A99B8CBC0 FOREIGN KEY (user_uuid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7ADDF9797C FOREIGN KEY (board_id_id) REFERENCES board (id)');
        $this->addSql('CREATE INDEX IDX_FBCE3E7ADDF9797C ON subject (board_id_id)');
        $this->addSql('CREATE INDEX IDX_FBCE3E7A99B8CBC0 ON subject (user_uuid_id)');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A76ED395');
        $this->addSql('DROP INDEX IDX_64C19C1A76ED395 ON category');
        $this->addSql('ALTER TABLE category CHANGE user_id user_uuid_id INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C199B8CBC0 FOREIGN KEY (user_uuid_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_64C19C199B8CBC0 ON category (user_uuid_id)');
        $this->addSql('ALTER TABLE board DROP FOREIGN KEY FK_58562B4712469DE2');
        $this->addSql('ALTER TABLE board DROP FOREIGN KEY FK_58562B47A76ED395');
        $this->addSql('DROP INDEX IDX_58562B4712469DE2 ON board');
        $this->addSql('DROP INDEX IDX_58562B47A76ED395 ON board');
        $this->addSql('ALTER TABLE board ADD category_id_id INT NOT NULL, ADD user_uuid_id INT NOT NULL, DROP category_id, DROP user_id');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B479777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B4799B8CBC0 FOREIGN KEY (user_uuid_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_58562B479777D11E ON board (category_id_id)');
        $this->addSql('CREATE INDEX IDX_58562B4799B8CBC0 ON board (user_uuid_id)');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610537A1329');
        $this->addSql('DROP INDEX IDX_8C9F3610537A1329 ON file');
        $this->addSql('ALTER TABLE file CHANGE message_id message_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361080E261BC FOREIGN KEY (message_id_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_8C9F361080E261BC ON file (message_id_id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F23EDC87');
        $this->addSql('DROP INDEX IDX_B6BD307FA76ED395 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F23EDC87 ON message');
        $this->addSql('ALTER TABLE message ADD subject_id_id INT NOT NULL, ADD user_uuid_id INT NOT NULL, DROP user_id, DROP subject_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F99B8CBC0 FOREIGN KEY (user_uuid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6ED75F8F FOREIGN KEY (subject_id_id) REFERENCES subject (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F99B8CBC0 ON message (user_uuid_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F6ED75F8F ON message (subject_id_id)');
    }
}
