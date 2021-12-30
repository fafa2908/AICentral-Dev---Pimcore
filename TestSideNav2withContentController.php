<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use \Pimcore\Model\DataObject;

class TestSideNav2withContentController extends FrontendController
{
    /**
     * @Template()
     * @Route("/Test Side Nav 2 with Content", name="TestSideNav2withContent")
     */    
      public function defaultAction(Request $request){

        $eventList = new DataObject\CalendarEvents\Listing();
          
          // DATE TIME (GMT +8)
          foreach($eventList as $event){
              $event->getStartDateTime()->modify('+8 hours');
              $event->getEndDateTime()->modify('+8 hours'); 
          }
          
          
          
          return $this->render('TestSideNav2withContent.html.twig',[
            "eventList" => $eventList
            ]);
    }

    
    /**
     * @Route("/Test Side Nav 2 with Content", name="TestSideNav2withContentFail")
     */    
      public function fallback(Request $request){
         return $this->render('default/default.html.twig');
     }
    
}
