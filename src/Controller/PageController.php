<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PageController extends FrontendController
{

    public function shopPageAction(Request $request): Response
    {
        $category = $request->get("category");
        $entries = new DataObject\Package\Listing();
        if($category!=null){
            $entries->setCondition("category LIKE ?", ["%$category%"]);
        }
        $packageList = $entries->load();
        return $this->render('shop/all-products.html.twig', ['packageList' => $packageList]);
    }

    public function headerAction(Request $request): Response
    {
        return $this->render('snippets/header-snippet.html.twig');
    }

    public function footerAction(Request $request): Response
    {
        return $this->render('snippets/footer-snippet.html.twig');
    }

}
