<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Status;

class CookieConsentTranslationsExtController
{
    /** @var EntityRepository */
    protected $cookieConsentTranslationsRepository;
    
    public function __construct(
        EntityRepository $cookieConsentTranslationsRepository
    ) {
        $this->cookieConsentTranslationsRepository  = $cookieConsentTranslationsRepository;
    }
    
    public function getCookieConsentTranslationsAction(): Response
    {
        $cookieConsentTranslations          = $this->cookieConsentTranslationsRepository->findAll();
        
        $cookieConsentTranslationsResponse  = [];
        foreach ( $cookieConsentTranslations as $trans ) {
            $template   = $this->cookieConsentTranslationTemplate();
            
            $template["consent_modal"]["title"]                 = $trans->getTitle();
            $template["consent_modal"]["description"]           = $trans->getDescription();
            $template["consent_modal"]["primary_btn"]["text"]   = $trans->getBtnAcceptAll();
            $template["consent_modal"]["secondary_btn"]["text"] = $trans->getBtnRejectAll();
            
            $cookieConsentTranslationsResponse[$trans->getLanguageCode()]   = $template;
        }
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'response'  => $cookieConsentTranslationsResponse,
        ]);
    }
    
    private function cookieConsentTranslationTemplate()
    {
        return [
            "consent_modal"     => [
                "title"             => "We use cookies!",
                "description"       => "Description Text",
                "revision_message"  => "<br> Dude, my terms have changed. Sorry for bothering you again!",
                "primary_btn"       => [
                    "text"  => "Accept all",
                    "role"  => "accept_all"
                ],
                "secondary_btn"     => [
                    "text"  => "Reject all",
                    "role"  => "accept_necessary"
                ]
            ],
            
            "settings_modal"    => [
                "title"                 => "Cookie preferences",
                "save_settings_btn"     => "Save settings",
                "accept_all_btn"        => "Accept all",
                "reject_all_btn"        => "Reject all",
                "close_btn_label"       => "Close",
                
                "cookie_table_headers"  => [
                    [
                        "col1"  => "Name"
                    ],
                    [
                        "col2"  => "Domain"
                    ],
                    [
                        "col3"  => "Expiration"
                    ],
                    [
                        "col4"  => "Description"
                    ]
                ],
                
                "blocks"                => [
                    [
                        "title"         => "Strictly necessary cookies",
                        "description"   => "Description Text",
                        "toggle"        => [
                            "value"     => "necessary",
                            "enabled"   => true,
                            "readonly"  => true
                        ]
                    ]
                ]
            ]
        ];
    }
}