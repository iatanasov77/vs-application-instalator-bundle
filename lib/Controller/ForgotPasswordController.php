<?php namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    /**
     * @Route("/forgot-password", name="app_page_forgot_password")
     */
    public function index()
    {
        return $this->render( '@VSUsers/Reseting/forgot_password.html.twig' );
    }
}
