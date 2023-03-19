<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\IssueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Simply renders the home page
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(IssueRepository $issueRepository, CommentRepository $commentRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'assigned_issues' => $issueRepository->findByAssignee($this->getUser()),
            'recent_comments' => $commentRepository->findRecentComments(),
            'recent_issues' => $issueRepository->findRecentIssues()
        ]);
    }
}
