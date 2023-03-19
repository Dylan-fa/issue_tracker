<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Issue;
use App\Form\CommentFormType;
use App\Form\CreateIssueFormType;
use App\Form\DeleteCommentFormType;
use App\Form\EditIssueFormType;
use App\Repository\CommentRepository;
use App\Repository\IssueRepository;
use DateTimeImmutable;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * This controller handles all issue related resources
 */
class IssueController extends AbstractController
{
    #[Route('/issue/{id}', name: 'app_issue', requirements: ['id' => '\d+'])]
    public function show(Issue $issue, Request $request, CommentRepository $commentRepository, FormFactoryInterface $formFactory): Response
    {
        $comment = new Comment();
        // Manually name forms as they are being output twice on the same page
        // and need to be differentiated.
        $createCommentForm = $formFactory->createNamed('create_comment_form', CommentFormType::class, $comment);
        $createCommentForm->handleRequest($request);
        $editCommentForm = $formFactory->createNamed('edit_comment_form', CommentFormType::class, $comment);
        $editCommentForm->handleRequest($request);
        $deleteCommentForm = $this->createForm(DeleteCommentFormType::class);
        $deleteCommentForm->handleRequest($request);
        // User is creating a new comment
        if ($createCommentForm->isSubmitted() && $createCommentForm->isValid()) {
            // Manually set the current user, the issue that the comment is posted on, and the current date
            $comment
                ->setAuthor($this->getUser())
                ->setIssue($issue)
                ->setCreatedAt(New DateTimeImmutable('now'))
            ;
            $commentRepository->save($comment, true);
            return $this->redirectToRoute('app_issue', ['id' => $issue->getId(), '_fragment' => 'comment-' . $comment->getId()]);
        // User is editing an existing comment
        } elseif($editCommentForm->isSubmitted() && $editCommentForm->isValid()) {
            // Find the comment that the user wants to edit by ID
            $comment = $commentRepository->find($editCommentForm->get('id')->getData());
            // Update the text
            $comment->setText($editCommentForm->get('text')->getData());
            $commentRepository->save($comment, true);
            return $this->redirectToRoute('app_issue', ['id' => $issue->getId(), '_fragment' => 'comment-' . $comment->getId()]);
        } elseif($deleteCommentForm->isSubmitted() && $deleteCommentForm->isValid()) {
            $comment = $commentRepository->find($deleteCommentForm->get('id')->getData());
            $commentRepository->remove($comment, true);
        }
        // User is simply viewing the issue
        return $this->render('issue/show.html.twig', [
            'issue'         => $issue,
            'createCommentForm'   => $createCommentForm->createView(),
            'editCommentForm'   => $editCommentForm->createView(),
            'deleteCommentForm' => $deleteCommentForm->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/issue/{id}/edit', name: 'app_issue_edit', requirements: ['id' => '\d+'])]
    public function edit(IssueRepository $issueRepository, Request $request, Issue $issue): Response
    {
        $editForm = $this->createForm(EditIssueFormType::class, $issue);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) { 
            // User wants to delete the issue
            if ($editForm->get('delete')->isClicked()) {
                $issueRepository->remove($issue, true);
                return $this->redirectToRoute('app_home');
            }
            // User is just editing the issue
            $issueRepository->save($issue, true);
            $this->addFlash('success', 'Issue successfully updated!');
            return $this->redirectToRoute('app_issue', ['id' => $issue->getId()]);
        }
        return $this->render('issue/edit.html.twig', [
            'issueForm'     => $editForm->createView(),
            'issue'         => $issue,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/issue/create', name: 'app_issue_create')]
    public function create(Request $request, IssueRepository $issueRepository): Response
    {
        $issue = new Issue();
        $createForm = $this->createForm(CreateIssueFormType::class, $issue);
        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid()) {
            // manually add current user and current date
            $issue
                ->setCreatedAt(New DateTimeImmutable('now'))
                ->setAuthor($this->getUser())
            ;

            $issueRepository->save($issue, true);
            $this->addFlash('success', 'Issue successfully created!');
            return $this->redirectToRoute('app_issue', ['id' => $issue->getId()]);
        }

        return $this->render('issue/create.html.twig', [
            'issueForm'     => $createForm->createView(),
            'headerText'    => 'Create issue',
        ]);
    }
}
