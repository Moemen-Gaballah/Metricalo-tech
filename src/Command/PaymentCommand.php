<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\PaymentServiceInterface;

#[AsCommand(name: 'app:example')]
class PaymentCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('provider', InputArgument::REQUIRED, 'Payment provider (aci|shift4)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $input->getArgument('provider');
        $service = $this->resolveService($provider);

        if (!$service) {
            $output->writeln('<error>Invalid provider</error>');
            return Command::INVALID;
        }

        $response = $service->purchase([]);
        $output->writeln(json_encode($response, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}

