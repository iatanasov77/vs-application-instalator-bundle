<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface WidgetInterface extends ResourceInterface, TranslatableInterface
{
    public function getGroup(): ?WidgetGroupInterface;
    public function getCode(): string;
    public function getName(): string;
    public function getDescription(): string;
    public function getActive(): ?bool;
    public function isActive(): bool;
}