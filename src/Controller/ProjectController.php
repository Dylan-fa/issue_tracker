<?php

namespace App\Controller;

use App\Entity\KanbanColumn;
use App\Entity\KanbanColumnIssue;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Project;
use App\Form\AddColumnFormType;
use App\Form\AddIssueToColumnFormType;
use App\Form\CreateProjectFormType;
use App\Form\EditProjectFormType;
use App\Repository\KanbanColumnIssueRepository;
use App\Repository\KanbanColumnRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTimeImmutable;

/**
 * This controller handles all project related resources
 */
class ProjectController extends AbstractController
{
    #[Route('/project/{id}', name: 'app_project', requirements: ['id' => '\d+'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project
        ]);
    }

    #[Route('/project/{id}/kanban', name: 'app_project_kanban', requirements: ['id' => '\d+'])]
    public function kanban(Request $request, Project $project, KanbanColumnRepository $kanbanColumnRepository, KanbanColumnIssueRepository $kanbanColumnIssueRepository): Response
    {
        $kanbanColumn = new KanbanColumn();
        $addColumnForm = $this->createForm(AddColumnFormType::class, $kanbanColumn);
        $addColumnForm->handleRequest($request);
        if ($addColumnForm->isSubmitted() && $addColumnForm->isValid()) { 
            $kanbanColumn->setProject($project);
            $kanbanColumnRepository->save($kanbanColumn, true);
        }
        $kanbanColumnIssue = new KanbanColumnIssue();
        $addIssueForm = $this->createForm(AddIssueToColumnFormType::class, $kanbanColumnIssue);
        $addIssueForm ->handleRequest($request);
        if ($addIssueForm->isSubmitted() && $addIssueForm->isValid()) { 
            $kanbanColumn = $kanbanColumnRepository->find($addIssueForm->get('id')->getData());
            $kanbanColumnIssue->setKanbanColumn($kanbanColumn);
            $kanbanColumnIssue->setSequence(0);
            $kanbanColumnIssueRepository->save($kanbanColumnIssue, true);
        }
        return $this->render('project/kanban.html.twig', [
            'project' => $project,
            'addColumnForm' => $addColumnForm->createView(),
            'addIssueForm' => $addIssueForm->createView()
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/project/{id}/edit', name: 'app_project_edit', requirements: ['id' => '\d+'])]
    public function edit(Project $project, Request $request, ProjectRepository $projectRepository): Response
    {
        $editForm = $this->createForm(EditProjectFormType::class, $project);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($editForm->get('delete')->isClicked()) {
                $projectRepository->remove($project, true);
                return $this->redirectToRoute('app_home');
            }
            $projectRepository->save($project, true);
            $this->addFlash('success', 'Project successfully updated!');
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }
        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'projectForm'     => $editForm->createView(),
            'headerText'    => 'Edit project'
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/project/create', name: 'app_project_create')]
    public function create(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $createForm = $this->createForm(CreateProjectFormType::class, $project);
        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid()) {

            $project
                ->setCreatedAt(New DateTimeImmutable('now'))
                ->setCreatedBy($this->getUser())
            ;
            $projectRepository->save($project, true);
            $this->addFlash('success', 'Project successfully created!');
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }
        return $this->render('project/create.html.twig', [
            'projectForm'     => $createForm->createView(),
            'headerText'    => 'Create project',
        ]);
    }
}