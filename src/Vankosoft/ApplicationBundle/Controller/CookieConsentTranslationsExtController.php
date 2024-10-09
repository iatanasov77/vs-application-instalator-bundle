<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Cache\CacheItemPoolInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Status;

class CookieConsentTranslationsExtController extends AbstractController
{
    /** @var CacheItemPoolInterface */
    protected $cache;
    
    /** @var EntityRepository */
    protected $cookieConsentTranslationsRepository;
    
    public function __construct(
        CacheItemPoolInterface $cache,
        EntityRepository $cookieConsentTranslationsRepository
    ) {
        $this->cache                                = $cache;
        $this->cookieConsentTranslationsRepository  = $cookieConsentTranslationsRepository;
    }
    
    public function getCookieConsentTranslationsAction(): Response
    {
        $cacheKey   = 'cookie_consent_translations';
        $cache      = $this->cache->getItem( $cacheKey );
        
        if ( false === $cache->isHit() ) {
            
            $cookieConsentTranslationsObject    = $this->createTranslationsObject();
            $cookieConsentTranslationsJson      = \json_encode( $cookieConsentTranslationsObject );
            $cache->set( $cookieConsentTranslationsJson );
            
            $this->cache->save( $cache );
        }
        
        $cachedTranslations = $cache->get();
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'response'  => \json_decode( $cachedTranslations, true ),
        ]);
    }
    
    private function createTranslationsObject(): array
    {
        $cookieConsentTranslations          = $this->cookieConsentTranslationsRepository->findAll();
        
        $cookieConsentTranslationsObject    = [];
        foreach ( $cookieConsentTranslations as $trans ) {
            $template   = $this->cookieConsentTranslationTemplate();
            
            $template["consentModal"]["label"]          = $trans->getLabel();
            $template["consentModal"]["title"]          = $trans->getTitle();
            $template["consentModal"]["description"]    = $trans->getDescription();
            $template["consentModal"]["acceptAllBtn"]   = $trans->getBtnAcceptAll();
            $template["consentModal"]["rejectAllBtn"]   = $trans->getBtnRejectAll();
            
            $cookieConsentTranslationsObject[$trans->getLanguageCode()]   = $template;
        }
        
        return $cookieConsentTranslationsObject;
    }
    
    private function cookieConsentTranslationTemplate()
    {
        return [
            "consentModal"     => [
                "label"                 => "Cookie Consent",
                "title"                 => "We use cookies!",
                "description"           => "Description Text",
                "revision_message"      => "<br> Dude, my terms have changed. Sorry for bothering you again!",
                "acceptAllBtn"          => "Accept all",
                "rejectAllBtn"          => "Reject all",
                "acceptNecessaryBtn"    => "Accept necessary",
                "showPreferencesBtn"    => "Manage individual preferences",
            ],
            
            "preferencesModal"    => [
                "title"                 => "Cookie preferences",
                "acceptAllBtn"          => "Accept all",
                "acceptNecessaryBtn"    => "Accept necessary only",
                "savePreferencesBtn"    => "Accept current selection",
                "closeIconLabel"        => "Close modal",
                "sections"              => [
                    [
                        "title"             => "Strictly necessary cookies",
                        "description"       => "Description Text",
                        "linkedCategory"    => "necessary",
                    ]
                ],
            ]
        ];
    }
}