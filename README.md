# Orders

Başlamadan Önce:

Projeyi yerel bilgisayarınızda çalıştırmak için aşağıdaki adımları takip edebilirsiniz:

### 1) Projeyi bilgisayarınıza klonlayın:
```
git clone https://github.com/lnsyder/orders.git
cd orders/docker
```
### 2) Docker konteynerlarını ayağa kaldırın:
```
docker-compose up -d --build
```
### 3) Gerekli bağımlılıkları yükleyin:
```
docker exec -it orders-app bash -c "composer install"
```
### 4) Veritabanı bilgilerini girin:
.env dosyasına aşağıdaki parametreyi ekleyin:
```
DATABASE_URL="pgsql://user:password@postgres:5432/postgres?serverVersion=16&charset=utf8"
```
### 5) Oluşan Veritabanına; Tabloları, Discount strajeilerini ve Product Stock Trigger'ını eklemek için:
```
docker exec -it orders-app bash -c "bin/console doctrine:migrations:migrate"
```
### 6) Customer verisini çekmek için aşağıdaki command'ı tetikleyin:
```
docker exec -it orders-app bash -c "bin/console app:import-customers"
```
### 7) Product verisini çekmek için aşağıdaki command'ı tetikleyin:
```
docker exec -it orders-app bash -c "bin/console app:import-products"
```
### Uygulama artık http://localhost/ adresinden erişilebilir olacaktır.

### 8) Projenin kullanımı:
Ana dizinde paylaşılan Postman Collection'ını Postman'e import edin.

#### Order/Create (POST):
Sipariş oluşturmak için bu requesti kullanabilirsiniz.

#### Order/Update (PUT):
Mevcut bir siparişi güncellemek için bu requesti kullanabilirsiniz.

#### Order/List (GET):
Tüm siparişleri listelemek için bu requesti kullanabilirsiniz.

#### Order/Show (GET):
Mevcut bir siparişi görüntülemek için bu requesti kullanabilirsiniz.

#### Order/Delete (DELETE):
Mevcut bir siparişi silmek için bu requesti kullanabilirsiniz.

#### Discount/CalculateDiscounts (GET):
Mevcut bir siparişin indirimlerini hesaplamak için bu requesti kullanabilirsiniz.

### Kullanılan Teknolojiler:

PHP 8.2

Symfony Framework 7.2

PostgreSQL 16
