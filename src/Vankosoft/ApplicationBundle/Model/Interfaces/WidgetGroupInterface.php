<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonDescendentInterface;
use Doctrine\Common\Collections\Collection;

interface WidgetGroupInterface extends ResourceInterface, TaxonDescendentInterface
{
    public function getWidgets(): Collection;
}
