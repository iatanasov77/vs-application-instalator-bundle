<?php namespace Vankosoft\ApplicationBundle\Component;

use Symfony\Component\Cache\Adapter\DoctrineDbalAdapter;

class VsDoctrineDbalCache extends DoctrineDbalAdapter
{
    public function __construct( string $databaseDSN )
    {
        // https://github.com/symfony/symfony/blob/7.0/src/Symfony/Component/Cache/Adapter/DoctrineDbalAdapter.php
        $options    = [
            'db_table'  => 'VSAPP_DoctrineDbalCache'
        ];
        
        parent::__construct( $databaseDSN, '', 0, $options );
    }
}