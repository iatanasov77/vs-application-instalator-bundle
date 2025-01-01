<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface WidgetInterface extends ResourceInterface, TranslatableInterface
{
    public function getGroup(): ?WidgetGroupInterface;
    public function getCode(): string;
    public function getName(): string;
    public function getDescription(): ?string;
    public function getAllowedRoles(): Collection;
    public function getAllowedRolesFromCollection(): array;
    public function getActive(): ?bool;
    public function isActive(): bool;
}