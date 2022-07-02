<?php

namespace App\Controller;

use App\Entity\Visitor;
use App\Form\VisitorType;
use App\Repository\VisitorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/visitor")
 */
class VisitorController extends AbstractController
{
    /**
     * @Route("/", name="app_visitor_index", methods={"GET"})
     */
    public function index(VisitorRepository $visitorRepository): Response
    {
        return $this->render('visitor/index.html.twig', [
            'visitors' => $visitorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_visitor_show", methods={"GET"})
     */
    public function show(Visitor $visitor): Response
    {
        return $this->render('visitor/show.html.twig', [
            'visitor' => $visitor,
        ]);
    }

    /**
     * @Route("/{id}", name="app_visitor_delete", methods={"POST"})
     */
    public function delete(Request $request, Visitor $visitor, VisitorRepository $visitorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$visitor->getId(), $request->request->get('_token'))) {
            $visitorRepository->remove($visitor);
        }

        return $this->redirectToRoute('app_visitor_index', [], Response::HTTP_SEE_OTHER);
    }
}
