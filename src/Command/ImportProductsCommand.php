<?php

namespace App\Command;

use App\DTO\Request\ProductRequest;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-products',
    description: 'Import products from JSON file',
)]
class ImportProductsCommand extends Command
{
    private const PRODUCTS_URI = 'https://raw.githubusercontent.com/ideasoft/se-take-home-assessment/master/example-data/products.json';

    /**
     * @param Connection $connection
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly HttpClientInterface $httpClient
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $response = $this->httpClient->request('GET', self::PRODUCTS_URI);
            $products = json_decode(
                $response->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $this->connection->beginTransaction();

            foreach ($products as $productData) {
                try {
                    $productRequest = ProductRequest::fromArray($productData);

                    $this->connection->executeStatement(
                        'INSERT INTO product (id, name, category, price, stock) VALUES (:id, :name, :category, :price, :stock)',
                        [
                            'id' => $productData['id'],
                            'name' => $productRequest->name,
                            'category' => $productRequest->category,
                            'price' => $productRequest->price,
                            'stock' => $productRequest->stock
                        ]
                    );

                    $io->success(sprintf('Product imported: %s', $productRequest->name));
                } catch (\Exception $e) {
                    $io->error(sprintf('Error importing product: %s', $e->getMessage()));
                }
            }

            $this->connection->commit();
            $io->success('Products import completed successfully');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            if (isset($this->connection) && $this->connection->isTransactionActive()) {
                $this->connection->rollBack();
            }
            $io->error(sprintf('Import failed: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}