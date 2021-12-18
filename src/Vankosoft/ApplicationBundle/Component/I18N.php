<?php namespace Vankosoft\ApplicationBundle\Component;

class I18N
{
    public static function Languages()
    {
        return [
            'en'    => 'English',
            'en_US' => 'English (US)',
            'bg'    => 'Bulgarian',
            'bg_BG' => 'Bulgarian'
        ];
    }
    
    public static function LanguagesAvailable()
    {
        $langs      = self::Languages();
        $ret        = [];
        
        if ( isset( $_ENV['LANGUAGES'] ) ) {
            $envLangs   = explode( ',', $_ENV['LANGUAGES'] );
            foreach( $envLangs as $l ) {
                $ret[$l]    = isset( $langs[$l] ) ? $langs[$l] : 'Lang not available in this environement';
            }
        }
        
        return $ret;
    }
}
