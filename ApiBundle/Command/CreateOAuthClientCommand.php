<?php
/**
 * Created by PhpStorm.
 * User: jannatul
 * Date: 21/04/16
 * Time: 16:17
 */

namespace Reviewer\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateOAuthClientCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oauth:client:create')
            ->setDescription('Create OAuth Client')
            ->addArgument(
                'redirectUri',
                InputArgument::REQUIRED,
                'Redirect URI?'
            )
            ->addArgument(
                'grantType',
                InputArgument::REQUIRED,
                'Grant Type?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $redirectUri = $input->getArgument('redirectUri');
        $grantType = $input->getArgument('grantType');

        $clientManager = $container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris([$redirectUri]);
        $client->setAllowedGrantTypes([$grantType]);
        $clientManager->updateClient($client);

        $output->writeln(sprintf("<info>The client was created with <comment>%s</comment> as public id and <comment>%s</comment> as secret</info>",
            $client->getPublicId(),
            $client->getSecret()
        ));

        $output->writeln("This is the request you need to make to retrieve the access token. Replace username and password with your own.");
        $output->writeln(sprintf("<info>/oauth/v2/token?client_id=%s&client_secret=%s&grant_type=%s&redirect_uri=%s&<comment>username={username}&password={password}</comment></info>",
            $client->getPublicId(),
            $client->getSecret(),
            $client->getAllowedGrantTypes()[0],
            $client->getRedirectUris()[0]
        ));
    }
}