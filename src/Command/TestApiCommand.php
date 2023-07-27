<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:test-api',
    description: 'Add a short description for your command',
)]
class TestApiCommand extends Command
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
{
    parent::__construct();
    
}

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reponse = $this->httpClient->request ('POST', "http://api.brevo.com/v3/smtp/email", [
            "headers" => [
                "accept" => "application/json",
                "api-key" => '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', 
                "contentType" => "application/json",
            ], 
            'json' => [
                "sender" => [
                    'name' => "Jules Pauly", 
                    "email" => 'bahaaeddine.ghezz@gmail.com',
                ],
                "to" => [
                    [
                        "email" => 'bahaaeddine.ghezz@gmail.com', 
                        "name" => "Jules Pauly",
                    ]
        
                ], 
                
                "subject" => "Bonjour ! ", 
                "htmlContent" => "<p> Salut je te dis bonjour !</p>",
            ]
        ]); 
        return Command::SUCCESS;
    }
}
