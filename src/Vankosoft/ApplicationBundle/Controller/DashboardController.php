<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    public function index()
    {
        return $this->render( '@VSApplication/Pages/Dashboard/home.html.twig' );
    }
}
