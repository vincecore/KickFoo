<?php

namespace KickFoo\Infrastructure\Twig;

use KickFoo\Domain\Entity\Player;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Extension;
use Twig_SimpleFilter;

class PlayerExtension extends Twig_Extension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * PlayerExtension constructor
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('player', array($this, 'playerFilter')),
        );
    }

    public function playerFilter(Player $player)
    {
        return '<a href="'.$this->urlGenerator->generate('player_detail', array('id' => $player->getId())).'">'.$player->getFirstname().'</a>';
    }

    public function getName()
    {
        return 'player_extension';
    }
}
