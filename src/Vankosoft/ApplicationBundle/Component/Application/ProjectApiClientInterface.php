<?php namespace Vankosoft\ApplicationBundle\Component\Application;

interface ProjectApiClientInterface
{
    /**
     * Login to VankoSoft API
     *
     * @throws VankosoftApiException
     * @return string Vankosoft API Token
     */
    public function login(): string;
}