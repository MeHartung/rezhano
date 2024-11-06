<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181019111424 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_document (id INT NOT NULL, `show` TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_document (id INT NOT NULL, user_id INT DEFAULT NULL, document_type_id INT NOT NULL, uuid VARCHAR(255) DEFAULT NULL, INDEX IDX_38E46E76A76ED395 (user_id), INDEX IDX_38E46E7661232A4F (document_type_id), UNIQUE INDEX user_type (user_id, document_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_document_type (id INT NOT NULL, show_individual TINYINT(1) DEFAULT \'0\' NOT NULL, show_juridical TINYINT(1) DEFAULT \'0\' NOT NULL, show_enterpreneur TINYINT(1) DEFAULT \'0\' NOT NULL, position_individual INT DEFAULT 0 NOT NULL, position_juridical INT DEFAULT 0 NOT NULL, position_enterpreneur INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, head TINYINT(1) NOT NULL, foot TINYINT(1) NOT NULL, tree_left INT NOT NULL, tree_level INT NOT NULL, tree_right INT NOT NULL, INDEX IDX_7D053A93A977936C (tree_root), INDEX IDX_7D053A93727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_templates (alias VARCHAR(64) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, PRIMARY KEY(alias)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, readAt DATETIME DEFAULT NULL, is_read TINYINT(1) DEFAULT \'0\' NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_dialog (id INT NOT NULL, dialog_id INT NOT NULL, UNIQUE INDEX UNIQ_81C6263F5E46C4E2 (dialog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_order (id INT NOT NULL, order_id INT DEFAULT NULL, message LONGTEXT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_339D76618D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_text (id INT NOT NULL, notification_type VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (name VARCHAR(50) NOT NULL, value LONGTEXT DEFAULT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brands (id INT AUTO_INCREMENT NOT NULL, virtuemart_manufacturer_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_attributes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, units VARCHAR(1020) DEFAULT NULL, type INT NOT NULL, value_type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_attribute_value (id INT AUTO_INCREMENT NOT NULL, product_attribute_id INT DEFAULT NULL, value VARCHAR(1024) NOT NULL, INDEX IDX_CCC4BE1F3B420C91 (product_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_types_to_product_attributes (product_type_id INT NOT NULL, product_attribute_id INT NOT NULL, INDEX IDX_E8523A9414959723 (product_type_id), INDEX IDX_E8523A943B420C91 (product_attribute_id), PRIMARY KEY(product_type_id, product_attribute_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, product_type_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, short_description VARCHAR(1024) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, old_price NUMERIC(10, 2) DEFAULT NULL, is_sale TINYINT(1) DEFAULT NULL, is_hit TINYINT(1) DEFAULT NULL, is_novice TINYINT(1) DEFAULT NULL, is_with_gift TINYINT(1) NOT NULL, sku VARCHAR(255) NOT NULL, is_publication_allowed TINYINT(1) NOT NULL, published TINYINT(1) NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, length DOUBLE PRECISION DEFAULT NULL, width DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, volume DOUBLE PRECISION DEFAULT NULL, units VARCHAR(16) DEFAULT NULL, is_purchasable TINYINT(1) DEFAULT NULL, purchase_price NUMERIC(10, 2) DEFAULT NULL, created_at DATETIME NOT NULL, total_stock INT NOT NULL, reserved_stock INT NOT NULL, is_free_delivery TINYINT(1) NOT NULL, rank DOUBLE PRECISION NOT NULL, INDEX IDX_B3BA5A5A44F5D008 (brand_id), INDEX IDX_B3BA5A5A14959723 (product_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_to_taxons (product_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_2EFE3DF34584665A (product_id), INDEX IDX_2EFE3DF3DE13F470 (taxon_id), PRIMARY KEY(product_id, taxon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_attribute_values_to_products (product_id INT NOT NULL, product_attribute_value_id INT NOT NULL, INDEX IDX_2E9AAE654584665A (product_id), INDEX IDX_2E9AAE659774A42E (product_attribute_value_id), PRIMARY KEY(product_id, product_attribute_value_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_images (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, virtuemart_media_id INT DEFAULT NULL, filename VARCHAR(512) NOT NULL, position INT NOT NULL, INDEX IDX_8263FFCE4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_questions (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, INDEX IDX_E47CE2584584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_rank (product_id INT NOT NULL, nb_views INT DEFAULT 0 NOT NULL, nb_cart INT DEFAULT 0 NOT NULL, nb_favorites INT DEFAULT 0 NOT NULL, nb_buy INT DEFAULT 0 NOT NULL, PRIMARY KEY(product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE presentation_options (taxon_id INT NOT NULL, options JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(taxon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalog_sections (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, alfa_bank_taxon_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, tree_left INT NOT NULL, tree_level INT NOT NULL, tree_right INT NOT NULL, image VARCHAR(255) DEFAULT NULL, short_name VARCHAR(255) NOT NULL, presentation_id INT DEFAULT NULL, nb_products INT DEFAULT 0 NOT NULL, INDEX IDX_19419D86A977936C (tree_root), INDEX IDX_19419D86727ACA70 (parent_id), INDEX IDX_19419D8689D223D5 (alfa_bank_taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalog_section_linked (catalog_section_id INT NOT NULL, catalog_section_linked_id INT NOT NULL, INDEX IDX_4EC505C76D0911A1 (catalog_section_id), INDEX IDX_4EC505C764D887C1 (catalog_section_linked_id), PRIMARY KEY(catalog_section_id, catalog_section_linked_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cdek_cities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code INT DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cdek_pickup_points (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, code INT DEFAULT NULL, postcode VARCHAR(6) DEFAULT NULL, address VARCHAR(512) NOT NULL, UNIQUE INDEX UNIQ_7E49E2248BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cdek_pvzlist (code VARCHAR(255) NOT NULL, owner_code VARCHAR(255) NOT NULL, coord_y VARCHAR(255) NOT NULL, coord_x VARCHAR(255) NOT NULL, work_time VARCHAR(255) NOT NULL, city_name VARCHAR(255) NOT NULL, city_code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, note VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_stock (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, product_id INT NOT NULL, value INT NOT NULL, reserved INT NOT NULL, INDEX IDX_EA6A2D3C5080ECDE (warehouse_id), INDEX IDX_EA6A2D3C4584665A (product_id), UNIQUE INDEX stock_unique (product_id, warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouses (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, latitude NUMERIC(9, 6) DEFAULT NULL, longitude NUMERIC(9, 6) DEFAULT NULL, INDEX IDX_AFE9C2B78BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, payment_method_id INT DEFAULT NULL, order_status_id INT DEFAULT NULL, user_id INT DEFAULT NULL, payment_status_id INT DEFAULT NULL, uid VARCHAR(36) NOT NULL, document_number VARCHAR(36) DEFAULT NULL, subtotal NUMERIC(10, 2) NOT NULL, shipping_cost NUMERIC(10, 2) DEFAULT NULL, fee NUMERIC(10, 2) DEFAULT NULL, total NUMERIC(10, 2) NOT NULL, discount_sum NUMERIC(10, 2) NOT NULL, customer_first_name VARCHAR(255) DEFAULT NULL, customer_last_name VARCHAR(255) DEFAULT NULL, customer_phone VARCHAR(32) DEFAULT NULL, customer_email VARCHAR(255) DEFAULT NULL, customer_comment VARCHAR(1024) DEFAULT NULL, shipping_city_fias_aouid VARCHAR(36) DEFAULT NULL, shipping_city_name VARCHAR(255) DEFAULT NULL, shipping_post_code INT DEFAULT NULL, shipping_address VARCHAR(255) DEFAULT NULL, shipping_method_id VARCHAR(36) DEFAULT NULL, shipping_date DATETIME DEFAULT NULL, checkout_state_id INT NOT NULL, preoder_date DATE DEFAULT NULL, virtuemart_order_id INT DEFAULT NULL, checkout_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E52FFDEE5AA1164F (payment_method_id), INDEX IDX_E52FFDEED7707B45 (order_status_id), INDEX IDX_E52FFDEEA76ED395 (user_id), INDEX IDX_E52FFDEE28DE2F95 (payment_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, virtuemart_order_item_id INT DEFAULT NULL, INDEX IDX_62809DB08D9F6D38 (order_id), INDEX IDX_62809DB04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_payment_statuses (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_69204865C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_payment_status_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(124) NOT NULL, guid VARCHAR(124) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_statuses (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, transition_notification_template_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, send_notification TINYINT(1) NOT NULL, INDEX IDX_AA08FCA0C54C8C93 (type_id), INDEX IDX_AA08FCA01D415705 (transition_notification_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status_history (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, status_id INT DEFAULT NULL, user_id INT DEFAULT NULL, reason VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_471AD77E8D9F6D38 (order_id), INDEX IDX_471AD77E6BF700BD (status_id), INDEX IDX_471AD77EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status_reasons (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status_notification_templates (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_order_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE alfabank_credit_taxons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_methods (id INT AUTO_INCREMENT NOT NULL, type_guid VARCHAR(255) DEFAULT NULL, availability_decision_manager_id VARCHAR(36) NOT NULL, fee_calculator_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(512) DEFAULT NULL, enabled TINYINT(1) NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, announce VARCHAR(1024) NOT NULL, text LONGTEXT NOT NULL, published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, teaser_image_options JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_BFDD3168989D9B62 (slug), UNIQUE INDEX UNIQ_BFDD31682B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_hotspot (alias VARCHAR(128) NOT NULL, title VARCHAR(256) DEFAULT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(alias)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dialogs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, dialog_type VARCHAR(255) NOT NULL, INDEX IDX_B8F7AEA7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dialog_messages (id INT AUTO_INCREMENT NOT NULL, dialog_id INT DEFAULT NULL, user_id INT DEFAULT NULL, message LONGTEXT NOT NULL, user_name VARCHAR(255) DEFAULT NULL, user_email VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_20FB43F35E46C4E2 (dialog_id), INDEX IDX_20FB43F3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, announce VARCHAR(1024) NOT NULL, text LONGTEXT NOT NULL, published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, published_at DATETIME DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, teaser_image_options JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_1DD39950989D9B62 (slug), UNIQUE INDEX UNIQ_1DD399502B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE special_offers (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, announce VARCHAR(1024) NOT NULL, text LONGTEXT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, teaser_image_options JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', published TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_DB314B8F989D9B62 (slug), UNIQUE INDEX UNIQ_DB314B8F2B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE companies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, inn VARCHAR(12) NOT NULL, kpp VARCHAR(50) NOT NULL, ogrn VARCHAR(15) NOT NULL, country VARCHAR(50) NOT NULL, address VARCHAR(255) NOT NULL, director VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, city_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', firstname VARCHAR(50) DEFAULT NULL, lastname VARCHAR(50) DEFAULT NULL, middlename VARCHAR(50) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, is_contragent TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_1483A5E9C05FB297 (confirmation_token), INDEX IDX_1483A5E9979B1AD6 (company_id), INDEX IDX_1483A5E98BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_lists (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, list_type VARCHAR(255) NOT NULL, INDEX IDX_A97AE4F9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_list_product (product_list_id INT NOT NULL, product_id INT NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_82A1FBCBEC770D3B (product_list_id), INDEX IDX_82A1FBCB4584665A (product_id), PRIMARY KEY(product_list_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE common_home_banner (id INT AUTO_INCREMENT NOT NULL, teaser VARCHAR(255) NOT NULL, url VARCHAR(1000) DEFAULT NULL, position INT DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registration_document ADD CONSTRAINT FK_E79CFDFEBF396750 FOREIGN KEY (id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_document ADD CONSTRAINT FK_38E46E76A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_document ADD CONSTRAINT FK_38E46E7661232A4F FOREIGN KEY (document_type_id) REFERENCES user_document_type (id)');
        $this->addSql('ALTER TABLE user_document ADD CONSTRAINT FK_38E46E76BF396750 FOREIGN KEY (id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_document_type ADD CONSTRAINT FK_C5E476EABF396750 FOREIGN KEY (id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93A977936C FOREIGN KEY (tree_root) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93727ACA70 FOREIGN KEY (parent_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notification_dialog ADD CONSTRAINT FK_81C6263F5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialogs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_dialog ADD CONSTRAINT FK_81C6263FBF396750 FOREIGN KEY (id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_order ADD CONSTRAINT FK_339D76618D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE notification_order ADD CONSTRAINT FK_339D7661BF396750 FOREIGN KEY (id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_text ADD CONSTRAINT FK_83B7ECFDBF396750 FOREIGN KEY (id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_attribute_value ADD CONSTRAINT FK_CCC4BE1F3B420C91 FOREIGN KEY (product_attribute_id) REFERENCES product_attributes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_types_to_product_attributes ADD CONSTRAINT FK_E8523A9414959723 FOREIGN KEY (product_type_id) REFERENCES product_types (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_types_to_product_attributes ADD CONSTRAINT FK_E8523A943B420C91 FOREIGN KEY (product_attribute_id) REFERENCES product_attributes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A44F5D008 FOREIGN KEY (brand_id) REFERENCES brands (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A14959723 FOREIGN KEY (product_type_id) REFERENCES product_types (id)');
        $this->addSql('ALTER TABLE products_to_taxons ADD CONSTRAINT FK_2EFE3DF34584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_to_taxons ADD CONSTRAINT FK_2EFE3DF3DE13F470 FOREIGN KEY (taxon_id) REFERENCES catalog_sections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_attribute_values_to_products ADD CONSTRAINT FK_2E9AAE654584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE product_attribute_values_to_products ADD CONSTRAINT FK_2E9AAE659774A42E FOREIGN KEY (product_attribute_value_id) REFERENCES product_attribute_value (id)');
        $this->addSql('ALTER TABLE product_images ADD CONSTRAINT FK_8263FFCE4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE product_questions ADD CONSTRAINT FK_E47CE2584584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE product_rank ADD CONSTRAINT FK_591CA444584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE presentation_options ADD CONSTRAINT FK_4D46BADEDE13F470 FOREIGN KEY (taxon_id) REFERENCES catalog_sections (id)');
        $this->addSql('ALTER TABLE catalog_sections ADD CONSTRAINT FK_19419D86A977936C FOREIGN KEY (tree_root) REFERENCES catalog_sections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalog_sections ADD CONSTRAINT FK_19419D86727ACA70 FOREIGN KEY (parent_id) REFERENCES catalog_sections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalog_sections ADD CONSTRAINT FK_19419D8689D223D5 FOREIGN KEY (alfa_bank_taxon_id) REFERENCES alfabank_credit_taxons (id)');
        $this->addSql('ALTER TABLE catalog_section_linked ADD CONSTRAINT FK_4EC505C76D0911A1 FOREIGN KEY (catalog_section_id) REFERENCES catalog_sections (id)');
        $this->addSql('ALTER TABLE catalog_section_linked ADD CONSTRAINT FK_4EC505C764D887C1 FOREIGN KEY (catalog_section_linked_id) REFERENCES catalog_sections (id)');
        $this->addSql('ALTER TABLE cdek_pickup_points ADD CONSTRAINT FK_7E49E2248BAC62AF FOREIGN KEY (city_id) REFERENCES cdek_cities (id)');
        $this->addSql('ALTER TABLE product_stock ADD CONSTRAINT FK_EA6A2D3C5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_stock ADD CONSTRAINT FK_EA6A2D3C4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouses ADD CONSTRAINT FK_AFE9C2B78BAC62AF FOREIGN KEY (city_id) REFERENCES cdek_cities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE5AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_methods (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEED7707B45 FOREIGN KEY (order_status_id) REFERENCES order_statuses (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE28DE2F95 FOREIGN KEY (payment_status_id) REFERENCES order_payment_statuses (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE order_payment_statuses ADD CONSTRAINT FK_69204865C54C8C93 FOREIGN KEY (type_id) REFERENCES order_payment_status_types (id)');
        $this->addSql('ALTER TABLE order_statuses ADD CONSTRAINT FK_AA08FCA0C54C8C93 FOREIGN KEY (type_id) REFERENCES order_status_types (id)');
        $this->addSql('ALTER TABLE order_statuses ADD CONSTRAINT FK_AA08FCA01D415705 FOREIGN KEY (transition_notification_template_id) REFERENCES order_status_notification_templates (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E6BF700BD FOREIGN KEY (status_id) REFERENCES order_statuses (id)');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dialogs ADD CONSTRAINT FK_B8F7AEA7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F35E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialogs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E98BAC62AF FOREIGN KEY (city_id) REFERENCES cdek_cities (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE product_lists ADD CONSTRAINT FK_A97AE4F9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE product_list_product ADD CONSTRAINT FK_82A1FBCBEC770D3B FOREIGN KEY (product_list_id) REFERENCES product_lists (id)');
        $this->addSql('ALTER TABLE product_list_product ADD CONSTRAINT FK_82A1FBCB4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registration_document DROP FOREIGN KEY FK_E79CFDFEBF396750');
        $this->addSql('ALTER TABLE user_document DROP FOREIGN KEY FK_38E46E76BF396750');
        $this->addSql('ALTER TABLE user_document_type DROP FOREIGN KEY FK_C5E476EABF396750');
        $this->addSql('ALTER TABLE user_document DROP FOREIGN KEY FK_38E46E7661232A4F');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93A977936C');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93727ACA70');
        $this->addSql('ALTER TABLE notification_dialog DROP FOREIGN KEY FK_81C6263FBF396750');
        $this->addSql('ALTER TABLE notification_order DROP FOREIGN KEY FK_339D7661BF396750');
        $this->addSql('ALTER TABLE notification_text DROP FOREIGN KEY FK_83B7ECFDBF396750');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A44F5D008');
        $this->addSql('ALTER TABLE product_attribute_value DROP FOREIGN KEY FK_CCC4BE1F3B420C91');
        $this->addSql('ALTER TABLE product_types_to_product_attributes DROP FOREIGN KEY FK_E8523A943B420C91');
        $this->addSql('ALTER TABLE product_attribute_values_to_products DROP FOREIGN KEY FK_2E9AAE659774A42E');
        $this->addSql('ALTER TABLE product_types_to_product_attributes DROP FOREIGN KEY FK_E8523A9414959723');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A14959723');
        $this->addSql('ALTER TABLE products_to_taxons DROP FOREIGN KEY FK_2EFE3DF34584665A');
        $this->addSql('ALTER TABLE product_attribute_values_to_products DROP FOREIGN KEY FK_2E9AAE654584665A');
        $this->addSql('ALTER TABLE product_images DROP FOREIGN KEY FK_8263FFCE4584665A');
        $this->addSql('ALTER TABLE product_questions DROP FOREIGN KEY FK_E47CE2584584665A');
        $this->addSql('ALTER TABLE product_rank DROP FOREIGN KEY FK_591CA444584665A');
        $this->addSql('ALTER TABLE product_stock DROP FOREIGN KEY FK_EA6A2D3C4584665A');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB04584665A');
        $this->addSql('ALTER TABLE product_list_product DROP FOREIGN KEY FK_82A1FBCB4584665A');
        $this->addSql('ALTER TABLE products_to_taxons DROP FOREIGN KEY FK_2EFE3DF3DE13F470');
        $this->addSql('ALTER TABLE presentation_options DROP FOREIGN KEY FK_4D46BADEDE13F470');
        $this->addSql('ALTER TABLE catalog_sections DROP FOREIGN KEY FK_19419D86A977936C');
        $this->addSql('ALTER TABLE catalog_sections DROP FOREIGN KEY FK_19419D86727ACA70');
        $this->addSql('ALTER TABLE catalog_section_linked DROP FOREIGN KEY FK_4EC505C76D0911A1');
        $this->addSql('ALTER TABLE catalog_section_linked DROP FOREIGN KEY FK_4EC505C764D887C1');
        $this->addSql('ALTER TABLE cdek_pickup_points DROP FOREIGN KEY FK_7E49E2248BAC62AF');
        $this->addSql('ALTER TABLE warehouses DROP FOREIGN KEY FK_AFE9C2B78BAC62AF');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E98BAC62AF');
        $this->addSql('ALTER TABLE product_stock DROP FOREIGN KEY FK_EA6A2D3C5080ECDE');
        $this->addSql('ALTER TABLE notification_order DROP FOREIGN KEY FK_339D76618D9F6D38');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB08D9F6D38');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E8D9F6D38');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE28DE2F95');
        $this->addSql('ALTER TABLE order_payment_statuses DROP FOREIGN KEY FK_69204865C54C8C93');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEED7707B45');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E6BF700BD');
        $this->addSql('ALTER TABLE order_statuses DROP FOREIGN KEY FK_AA08FCA01D415705');
        $this->addSql('ALTER TABLE order_statuses DROP FOREIGN KEY FK_AA08FCA0C54C8C93');
        $this->addSql('ALTER TABLE catalog_sections DROP FOREIGN KEY FK_19419D8689D223D5');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE5AA1164F');
        $this->addSql('ALTER TABLE notification_dialog DROP FOREIGN KEY FK_81C6263F5E46C4E2');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F35E46C4E2');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9979B1AD6');
        $this->addSql('ALTER TABLE user_document DROP FOREIGN KEY FK_38E46E76A76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77EA76ED395');
        $this->addSql('ALTER TABLE dialogs DROP FOREIGN KEY FK_B8F7AEA7A76ED395');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F3A76ED395');
        $this->addSql('ALTER TABLE product_lists DROP FOREIGN KEY FK_A97AE4F9A76ED395');
        $this->addSql('ALTER TABLE product_list_product DROP FOREIGN KEY FK_82A1FBCBEC770D3B');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE registration_document');
        $this->addSql('DROP TABLE user_document');
        $this->addSql('DROP TABLE user_document_type');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE email_templates');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_dialog');
        $this->addSql('DROP TABLE notification_order');
        $this->addSql('DROP TABLE notification_text');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE brands');
        $this->addSql('DROP TABLE product_attributes');
        $this->addSql('DROP TABLE product_attribute_value');
        $this->addSql('DROP TABLE product_types');
        $this->addSql('DROP TABLE product_types_to_product_attributes');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE products_to_taxons');
        $this->addSql('DROP TABLE product_attribute_values_to_products');
        $this->addSql('DROP TABLE product_images');
        $this->addSql('DROP TABLE product_questions');
        $this->addSql('DROP TABLE product_rank');
        $this->addSql('DROP TABLE presentation_options');
        $this->addSql('DROP TABLE catalog_sections');
        $this->addSql('DROP TABLE catalog_section_linked');
        $this->addSql('DROP TABLE cdek_cities');
        $this->addSql('DROP TABLE cdek_pickup_points');
        $this->addSql('DROP TABLE cdek_pvzlist');
        $this->addSql('DROP TABLE product_stock');
        $this->addSql('DROP TABLE warehouses');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE order_payment_statuses');
        $this->addSql('DROP TABLE order_payment_status_types');
        $this->addSql('DROP TABLE order_statuses');
        $this->addSql('DROP TABLE order_status_history');
        $this->addSql('DROP TABLE order_status_reasons');
        $this->addSql('DROP TABLE order_status_notification_templates');
        $this->addSql('DROP TABLE order_status_types');
        $this->addSql('DROP TABLE alfabank_credit_taxons');
        $this->addSql('DROP TABLE payment_methods');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE content_hotspot');
        $this->addSql('DROP TABLE dialogs');
        $this->addSql('DROP TABLE dialog_messages');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE special_offers');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE product_lists');
        $this->addSql('DROP TABLE product_list_product');
        $this->addSql('DROP TABLE common_home_banner');
    }
}
