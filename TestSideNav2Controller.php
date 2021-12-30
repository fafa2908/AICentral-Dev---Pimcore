<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use \Pimcore\Model\DataObject;

class TestSideNav2Controller extends FrontendController
{
    /**
     * @Template()
     * @Route("/Test Side Nav 2", name="TestSideNav2")
     */    
      public function defaultAction(Request $request){
        return $this->render('TestSideNav2.html.twig');
    }

    
    /**
     * @Route("/Test Side Nav 2", name="TestSideNav2Fail")
     */    
      public function fallback(Request $request){
         return $this->render('default/default.html.twig');
     }
    
}
