<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SurveyController extends AbstractController
{
    #[Route('/survey', name: 'survey_start')]
    public function start(SessionInterface $session): Response
    {
        $step = $session->get('survey_step', 1);
        return $this->redirectToRoute('survey_step', ['step' => $step]);
    }

    #[Route('/survey/{step}', name: 'survey_step', requirements: ['step' => '\d+'], methods: ['GET', 'POST'])]
    public function step(int $step, Request $request, SessionInterface $session): Response
    {
        $session->set('survey_step', $step);
        $data = $session->get('survey_data', []);

        if ($request->isMethod('POST')) {
            $data["step_$step"] = $request->request->all();
            $session->set('survey_data', $data);

            $this->addFlash('success', 'Данные сохранены');

            if ($step < 3) {
                return $this->redirectToRoute('survey_step', ['step' => $step + 1]);
            }

            return $this->redirectToRoute('survey_complete');
        }

        return $this->render("survey/step{$step}.html.twig", [
            'data' => $data["step_$step"] ?? []
        ]);
    }

    #[Route('/survey/complete', name: 'survey_complete', methods: ['GET', 'POST'])]
    public function complete(SessionInterface $session): Response
    {
        if ($this->getRequest()?->isMethod('POST')) {
            $data = $session->get('survey_data', []);

            $filename = 'survey_' . time() . '.json';
            file_put_contents($this->getParameter('kernel.project_dir') . "/var/$filename", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $session->clear();
            $this->addFlash('success', 'Опрос сохранён');

            return $this->redirectToRoute('survey_list');
        }

        return $this->render('survey/complete.html.twig');
    }

    #[Route('/survey/list', name: 'survey_list')]
    public function list(): Response
    {
        $files = glob($this->getParameter('kernel.project_dir') . '/var/survey_*.json');
        $surveys = [];

        foreach ($files as $file) {
            $surveys[] = json_decode(file_get_contents($file), true);
        }

        return $this->render('survey/list.html.twig', [
            'surveys' => $surveys
        ]);
    }
}
