<?php namespace Vankosoft\ApplicationBundle\Component\Application;

interface ProjectApiClientInterface
{
    protected function login(): string;
}