<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Doctrine\Common\Collections\Collection;

interface NestedTreeInterface
{
    public function getRoot(): ?self;
    public function getParent(): ?self;
    public function getChildren(): Collection;
    public function hasChild(NestedTreeInterface $entity): bool;
    public function hasChildren(): bool;
    public function addChild(NestedTreeInterface $entity): void;
    public function removeChild(NestedTreeInterface $entity): void;
}