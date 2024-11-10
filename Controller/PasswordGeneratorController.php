<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Hackzilla\PasswordGenerator\Exception\CharactersNotFoundException;

use Vankosoft\ApplicationBundle\Component\Status;

class PasswordGeneratorController extends AbstractController
{
    /** @var ComputerPasswordGenerator */
    private $computerPasswordGenerator;
    
    /**
     * NOTE: There Are More Types Of Password Generatos.
     * SEE: https://github.com/hackzilla/password-generator-bundle
     */
    public function __construct( ComputerPasswordGenerator $computerPasswordGenerator )
    {
        $this->computerPasswordGenerator    = $computerPasswordGenerator;
    }
    
    public function getPasswords( int $quantity, Request $request )
    {
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $this->generatePasswords( $quantity ),
        ]);
    }
    
    private function generatePasswords( int $quantity ): array
    {
        $passwords = $error = null;

        try {
            $passwords = $this->computerPasswordGenerator->generatePasswords( $quantity );
        } catch ( CharactersNotFoundException $e ) {
            $error = 'CharactersNotFoundException';
        }
        
        return [
            'passwords' => $passwords,
            'error'     => $error,
        ];
    }
}
