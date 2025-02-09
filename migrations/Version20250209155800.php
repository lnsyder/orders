<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209155800 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, name VARCHAR(255) NOT NULL, since DATE NOT NULL, revenue NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE discounts (id SERIAL NOT NULL, reason VARCHAR(255) NOT NULL, strategy_class VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE order_discounts (id SERIAL NOT NULL, order_id INT NOT NULL, discount_id INT NOT NULL, discount_reason VARCHAR(255) NOT NULL, discount_amount NUMERIC(10, 2) NOT NULL, subtotal NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_40D079408D9F6D38 ON order_discounts (order_id)');
        $this->addSql('CREATE INDEX IDX_40D079404C7C611F ON order_discounts (discount_id)');
        $this->addSql('CREATE TABLE order_product (id SERIAL NOT NULL, order_id INT NOT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, total NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2530ADE68D9F6D38 ON order_product (order_id)');
        $this->addSql('CREATE INDEX IDX_2530ADE64584665A ON order_product (product_id)');
        $this->addSql('CREATE TABLE orders (id SERIAL NOT NULL, customer_id INT NOT NULL, total NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEE9395C3F3 ON orders (customer_id)');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, name VARCHAR(255) NOT NULL, category INT NOT NULL, price NUMERIC(10, 2) NOT NULL, stock INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE order_discounts ADD CONSTRAINT FK_40D079408D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_discounts ADD CONSTRAINT FK_40D079404C7C611F FOREIGN KEY (discount_id) REFERENCES discounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Add BuyFiveGetOneStrategy
        $this->addSql("INSERT INTO public.discounts (id, reason, strategy_class, amount, created_at)
                            VALUES (DEFAULT, 'BuyFiveGetOneStrategy', 'App\Service\Discount\Strategy\BuyFiveGetOneStrategy', 0.00, '2025-02-09 19:06:39');");

        // Add TenPercentOverThousandStrategy
        $this->addSql("INSERT INTO public.discounts (id, reason, strategy_class, amount, created_at)
                            VALUES (DEFAULT, 'TenPercentOverThousandStrategy', 'App\Service\Discount\Strategy\TenPercentOverThousandStrategy', 0.00, '2025-02-09 19:07:27');");

        // Add CategoryDiscountStrategy
        $this->addSql("INSERT INTO public.discounts (id, reason, strategy_class, amount, created_at)
                            VALUES (DEFAULT, 'CategoryDiscountStrategy', 'App\Service\Discount\Strategy\CategoryDiscountStrategy', 0.00, '2025-02-09 19:07:11');");

        // Add handle_order_product_stock function //
        $this->addSql("CREATE OR REPLACE FUNCTION handle_order_product_stock()
                           RETURNS TRIGGER AS $$
                           DECLARE
                               v_current_stock INT;
                               v_product_name TEXT;
                           BEGIN
                               IF (TG_OP = 'INSERT') THEN
                                   SELECT stock, name INTO v_current_stock, v_product_name
                                   FROM product
                                   WHERE id = NEW.product_id
                                   FOR UPDATE;
                           
                                   IF v_current_stock < NEW.quantity THEN
                                       RAISE EXCEPTION 'Not enough stock for product \"%\". Required: %, Available: %',
                                           v_product_name, NEW.quantity, v_current_stock;
                                   END IF;
                           
                                   UPDATE product
                                   SET stock = stock - NEW.quantity
                                   WHERE id = NEW.product_id;
                           
                               ELSIF (TG_OP = 'UPDATE') AND OLD.product_id = NEW.product_id THEN
                                   SELECT stock, name INTO v_current_stock, v_product_name
                                   FROM product
                                   WHERE id = NEW.product_id
                                   FOR UPDATE;
                           
                                   UPDATE product
                                   SET stock = stock + OLD.quantity
                                   WHERE id = OLD.product_id;
                           
                                   SELECT stock INTO v_current_stock
                                   FROM product
                                   WHERE id = NEW.product_id;
                           
                                   IF v_current_stock < NEW.quantity THEN
                                       RAISE EXCEPTION 'Not enough stock for product \"%\". Required: %, Available: %',
                                           v_product_name, NEW.quantity, v_current_stock;
                                   END IF;
                           
                                   UPDATE product
                                   SET stock = stock - NEW.quantity
                                   WHERE id = NEW.product_id;
                           
                               ELSIF (TG_OP = 'DELETE') THEN
                                   UPDATE product
                                   SET stock = stock + OLD.quantity
                                   WHERE id = OLD.product_id;
                               END IF;
                           
                               RETURN COALESCE(NEW, OLD);
                           END;
                           $$ LANGUAGE plpgsql;");

        // Add order_product_stock_trigger //
        $this->addSql("CREATE TRIGGER order_product_stock_trigger
                           BEFORE INSERT OR UPDATE OR DELETE ON order_product
                           FOR EACH ROW
                           EXECUTE FUNCTION handle_order_product_stock();");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_discounts DROP CONSTRAINT FK_40D079408D9F6D38');
        $this->addSql('ALTER TABLE order_discounts DROP CONSTRAINT FK_40D079404C7C611F');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT FK_2530ADE68D9F6D38');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE9395C3F3');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE discounts');
        $this->addSql('DROP TABLE order_discounts');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE product');
    }
}
