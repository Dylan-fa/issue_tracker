<?php

namespace App\Controller;

use App\Repository\KanbanColumnIssueRepository;
use App\Repository\KanbanColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Spatie\CommonMarkShikiHighlighter\HighlightCodeExtension;


/**
 * This controller handles all API resources and recieves and returns a JSON response.
 * 
 * This controller is used by the frontend JavaScript to perform some realtime actions, e.g., previewing markdown or kanban board actions
 */
class ApiController extends AbstractController
{
    /**
     * This API method accepts one parameter 'text' which is to be parsed as markdown and returns a parameter 'parsed'
     */
    #[Route('/api/parse', name: 'app_api_parse')]
    public function parse(Request $request): Response
    {
        // Configure markdown parsing environment and add extension libraries
        $environment = (new Environment())
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new GithubFlavoredMarkdownExtension())
            ->addExtension(new HighlightCodeExtension('github-light'));
        $converter = new MarkdownConverter($environment);
        $text = json_decode($request->getContent())->text;
        $response = [
            'parsed' => $converter->convert($text)->getContent(),
        ];
        return $this->json($response);
    }
    /**
     * This API method accepts parameter 'columnID' and 'issueID' and removes the issue from the column, it returns parameter 'status' to indicate it was successful
     */
    #[Route('/api/kanban/removeissue', name: 'app_api_kanban_removeissue')]
    public function kanbanRemoveIssue(Request $request, KanbanColumnIssueRepository $kanbanColumnIssueRepository): Response
    {
        $json = json_decode($request->getContent());
        $issue = $kanbanColumnIssueRepository->find(['kanbanColumn' => $json->columnID, 'issue' => $json->issueID]);
        $kanbanColumnIssueRepository->remove($issue, true);
        $response = [
            'status' => 'success'
        ];
        return $this->json($response);
    }
    /**
     * This API method accepts parameter 'oldColumnID' and 'issueID' and moves the issue to the parameter 'newColumnID', it returns parameter 'status' to indicate it was successful
     */
    #[Route('/api/kanban/moveissue', name: 'app_api_kanban_moveissue')]
    public function kanbanMoveIssue(Request $request, KanbanColumnIssueRepository $kanbanColumnIssueRepository, KanbanColumnRepository $kanbanColumnRepository, EntityManagerInterface $em): Response
    {
        $json = json_decode($request->getContent());
        $issue = $kanbanColumnIssueRepository->find(['kanbanColumn' => $json->oldColumnID, 'issue' => $json->issueID]);
        $newColumn = $kanbanColumnRepository->find($json->newColumnID);
        $issue->setKanbanColumn($newColumn);
        $em->flush();
        $response = [
            'status' => 'success'
        ];
        return $this->json($response);
    }
}
