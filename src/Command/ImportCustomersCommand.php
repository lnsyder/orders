<?php

namespace App\Command;

use App\DTO\Request\CustomerRequest;
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
    name: 'app:import-customers',
    description: 'Import customers from JSON file',
)]
class ImportCustomersCommand extends Command
{
    private const CUSTOMERS_URI = 'https://raw.githubusercontent.com/ideasoft/se-take-home-assessment/master/example-data/customers.json';

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
            $response = $this->httpClient->request('GET', self::CUSTOMERS_URI);
            $customers = json_decode(
                $response->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $this->connection->beginTransaction();

            foreach ($customers as $customerData) {
                try {
                    $customerRequest = CustomerRequest::fromArray($customerData);

                    $this->connection->executeStatement(
                        'INSERT INTO customer (id, name, since, revenue) VALUES (:id, :name, :since, :revenue)',
                        [
                            'id' => $customerData['id'],
                            'name' => $customerRequest->name,
                            'since' => $customerRequest->since,
                            'revenue' => $customerRequest->revenue
                        ]
                    );

                    $io->success(sprintf('Customer imported: %s', $customerRequest->name));
                } catch (\Exception $e) {
                    $io->error(sprintf('Error importing customer: %s', $e->getMessage()));
                }
            }

            $this->connection->commit();
            $io->success('Customers import completed successfully');

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