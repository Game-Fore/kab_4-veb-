<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CookieController extends AbstractController
{
    #[Route('/cookie/accept', name: 'cookie_accept', methods: ['POST'])]
    public function accept(Request $request): RedirectResponse
    {
        $response = new RedirectResponse($request->headers->get('referer') ?? '/');

        // cookie_apply = true на 30 дней
        $cookie = new Cookie(
            'cookie_apply',
            'true',
            strtotime('+30 days')
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
