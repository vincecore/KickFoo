<?php
namespace KickFoo\Infrastructure\Console;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GravatarCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kickfoo:gravatar:import')
            ->setDescription('Import gravatar images for offline use');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lockfile = '/tmp/gravatar.lck';
        if (file_exists($lockfile)) {
            return;
        }
        touch($lockfile);
        $service = $this->getContainer()->get('kickfoo.service.gravatar');
        $playerRepository = $this->getContainer()->get('kickfoo.repository.player');

        $players = $playerRepository->findAllActivePlayers();
        foreach ($players as $player) {
            try {
                file_put_contents(dirname($this->getContainer()->get('kernel')->getRootDir()) . '/web/avatars/' . $player->getId() . '.jpg', $service->importAvatar($player));
            } catch (Exception $e) {
                unlink($lockfile);
                return;
            }
        }
        unlink($lockfile);
    }
}
