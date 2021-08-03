<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * All routes except those beginning with 'api' or 'back' are handled by vueJS routing.
     *
     * @Route("/{vueRouting}", name="vue_routing", requirements={ "vueRouting"="^((?!api|back).)*$" })
     *
     * @return Response
     */
    public function index()
    {
        return $this->render(
            'base.html.twig',
            [
                'authenticated' => (bool) $this->getUser(),
            ],
        );
    }
}
