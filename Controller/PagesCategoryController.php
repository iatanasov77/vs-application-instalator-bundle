<?php  namespace VS\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use VS\CmsBundle\Entity\PageCategory;
use VS\CmsBundle\Form\PageCategoryForm;

/**
 * Documentation
 * --------------
 * http://atlantic18.github.io/DoctrineExtensions/doc/tree.html
 *
 * Good example
 * -------------
 * http://drib.tech/programming/hierarchical-data-relational-databases-symfony-4-doctrine
 * https://github.com/dribtech/hierarchical-data-tutorial-part-2
 */
class PagesCategoryController extends ResourceController
{
    public function index( Request $request ): Response
    {
        $er = $this->getDoctrine()->getRepository( 'VS\CmsBundle\Entity\PageCategory' );
        
        $categories = $er->childrenHierarchy();
        //var_dump( $categories ); die;
        
        return $this->render( '@VSCms/Pages/category_index.html.twig', [
            'items'         => $categories,
            //'countProjects' => $er->countTotal()
        ]);
    }
    
    public function create( Request $request ): Response
    {
        $oCategory  = new PageCategory();
        $form       = $this->createForm( PageCategoryForm::class, $oCategory, ['data' => $oCategory, 'method' => 'POST'] );
        
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $form->getData() );
            $em->flush();
            
            return $this->redirect( $this->generateUrl( 'vs_cms_page_categories_index' ) );
        }
        
        return $this->render( '@VSCms/Pages/category_edit.html.twig', [
            'form'          => $form->createView(),
            'item'          => $oCategory,
        ]);
    }
    
    public function update( Request $request ) : Response
    {
        $er         = $this->getDoctrine()->getRepository( 'VS\CmsBundle\Entity\PageCategory' );
        $oCategory = $er->find( $request->attributes->get( 'id' ) );
        //var_dump( $oBlogPost->getChildren() ); die;
        $form       = $this->createForm( PageCategoryForm::class, $oCategory, ['data' => $oCategory, 'method' => 'PUT'] );
        
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $em         = $this->getDoctrine()->getManager();
            $category   = $form->getData();
            $category->setTranslatableLocale( $form['locale']->getData() );
            //$category->addTranslation( new \VS\CmsBundle\Entity\PageCategoryTranslation($form['locale']->getData(), 'name', $category->getName()));
            
            $em->persist( $category );
            $em->flush();
            
            return $this->redirect( $this->generateUrl( 'vs_cms_page_categories_index' ) );
        }
        
        return $this->render( '@VSCms/Pages/category_edit.html.twig', [
            'form'          => $form->createView(),
            'item'          => $oCategory,
        ]);
    }
}
