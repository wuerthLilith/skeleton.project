<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends FrontendController
{
    /**
     * @Route("/homepage", name="Startseite")
     */
    public function homeAction(Request $request): Response
    {
        return $this->render('homepage.html.twig', [
            'title' => 'Willkommen auf der Homepage',
        ]);
    }
}

