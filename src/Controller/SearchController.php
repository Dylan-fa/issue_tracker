<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\IssueRepository;
use App\Repository\ProjectRepository;

/**
 * This controller handles all search related resources
 */
class SearchController extends AbstractController
{
    #[Route('/search/issues', name: 'app_search_issues')]
    public function issues(IssueRepository $issueRepository, Request $request): Response
    {
        $issues = $issueRepository->findByTextQuery($request->query->get('search'));
        return $this->render('search/issues.html.twig', [
            'issues' => $issues,
            'searchValue' => $request->query->get('search')
        ]);
    }
    #[Route('/search/comments', name: 'app_search_comments')]
    public function comments(CommentRepository $commentsRepository, Request $request): Response
    {
        $comments = $commentsRepository->findByTextQuery($request->query->get('search'));
        return $this->render('search/comments.html.twig', [
            'comments' => $comments,
            'searchValue' => $request->query->get('search')
        ]);
    }
    #[Route('/search/projects', name: 'app_search_projects')]
    public function projects(ProjectRepository $projectRepository, Request $request): Response
    {
        $projects = $projectRepository->findByTextQuery($request->query->get('search'));
        return $this->render('search/projects.html.twig', [
            'projects' => $projects,
            'searchValue' => $request->query->get('search')
        ]);
    }
}