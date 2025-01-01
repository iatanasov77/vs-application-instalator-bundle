<?php namespace Vankosoft\ApplicationBundle\Model;

use Sylius\Component\Locale\Model\Locale as BaseLocale;
use Vankosoft\ApplicationBundle\Model\Traits\TranslatableTrait;
use Vankosoft\ApplicationBundle\Model\Interfaces\LocaleInterface;

class Locale extends BaseLocale implements LocaleInterface
{
    use TranslatableTrait;
    
    /**
     * @var string|null
     */
    protected $title;
    
    public function __construct()
    {
        $this->fallbackLocale   = 'en_US';
    }
    
    /**
     * There is getName() Method at \Sylius\Component\Locale\Model\Locale
     * 
     * {@inheritDoc}
     * @see \Vankosoft\ApplicationBundle\Model\Interfaces\LocaleInterface::getTitle()
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function setTitle($title): LocaleInterface
    {
        $this->title = $title;
        
        return $this;
    }
}
