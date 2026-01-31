<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookieController extends AbstractController
{
    #[Route('/cookie/accept', name: 'cookie_accept', methods: ['POST'])]
    public function accept(Request $request): Response
    {
        $response = $this->redirect($request->headers->get('referer') ?? '/');

        $response->headers->setCookie(
            new Cookie(
                'cookie_apply',
                'true',
                time() + 60 * 60 * 24 * 30 // 30 дней
            )
        );

        return $response;
    }
}
