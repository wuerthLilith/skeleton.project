<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Product;

class CatalogController extends FrontendController
{
    /**
     * @Route("/catalog", name="catalog")
     */
    public function catalogAction(Request $request): Response
    {
        $products = new Product\Listing();
        $products->setOrderKey("Artikelbezeichnung");
        $products->setOrder("ASC");

        return $this->render('catalog.html.twig', [
            'products' => $products->getData(),
        ]);
    }

    /**
     * @Route("/catalog/add", name="add_product")
     */
    public function addProductAction(Request $request): Response
{
    if ($request->isMethod('POST')) {

        $artikelnummer = $request->request->get('artikelnummer');
        $artikelbezeichnung = $request->request->get('artikelbezeichnung');
        $mengeneinheit = $request->request->get('mengeneinheit');
        $preis = (float) $request->request->get('preis');
        $hersteller = $request->request->get('hersteller');

        if (empty($artikelnummer)) {
            $artikelnummer = 'default-' . uniqid();
        }

        $product = new Product();

        $product->setKey(\Pimcore\Model\Element\Service::getValidKey($artikelnummer, 'object'));
        $product->setParentId(1); 

        $product->setArtikelnummer($artikelnummer);
        $product->setArtikelbezeichnung($artikelbezeichnung);
        $product->setMengeneinheit($mengeneinheit);
        $product->setPreis($preis);
        $product->setHersteller($hersteller);

        $product->setPDF(null); 

        $product->save(); 

        return $this->redirectToRoute('catalog'); 
    }


    return $this->render('addCatalog.html.twig');
}
}












