<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface WidgetGroupInterface extends ResourceInterface
{
    public function getName(): string;
    public function getWidgets(): Collection;
}
