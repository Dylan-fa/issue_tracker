<?php

namespace App\Controller;

use App\Form\IssueAtrributeConfigFormType;
use App\Form\UserRoleFormType;
use App\Repository\IssueAttributeRepositoryInterface;
use App\Repository\PriorityRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
/**
 * This controller handles all web resources related to configuring the issue tracker application
 */
class ConfigController extends AbstractController
{
    #[Route('/config', name: 'app_config')]
    public function index(): Response
    {
        return $this->render('config/index.html.twig');
    }

    // Common function for processing the editing of issue attributes e.g., (status, priority)
    public function processIssueAttributeEdit(Request $request, EntityManagerInterface $em, IssueAttributeRepositoryInterface $repository, $name): Response
    {
        $attributes = $repository->findAll();
        $issueAtrributeForm = $this->createForm(IssueAtrributeConfigFormType::class, [], ['attribute_data' => $attributes]);
        $issueAtrributeForm->handleRequest($request);
        $newAttributes = $issueAtrributeForm->get('attributes')->getData();
        if ($issueAtrributeForm->isSubmitted() && $issueAtrributeForm->isValid()) {   
            // Loop through all attributes currently in the database
            foreach($repository->findAll() as $oldAttribute) {
                // This attribute does not exist in the attributes submitted by the form
                // therefore the user removed it
                if(!in_array($oldAttribute, $newAttributes, true)) {
                    // only remoce the attribute if it is not in use
                    if($oldAttribute->getIssues()->count() === 0) {
                        $repository->remove($oldAttribute);
                    }
                    // This issue attribute is still in use because it has more than 0 uses
                    elseif($oldAttribute->getIssues()->count() !== 0) {
                        $error = new FormError('This ' . strtolower($name) . ' failed to delete because it is in use by an issue. Remove uses of this ' . strtolower($name) . ' to delete it.');
                        // Recreate form with error, don't flush changes
                        $issueAtrributeForm = $this->createForm(IssueAtrributeConfigFormType::class, [], ['attribute_data' => $attributes]);
                        $issueAtrributeForm->get('attributes')->get(array_search($oldAttribute, $attributes, true))->addError($error);
                        return $this->render('config/attribute-form.html.twig', [
                            'attributeForm' => $issueAtrributeForm->createView(),
                            'name' => $name
                        ]);
                    }
                }
            }
            foreach($newAttributes as $newAttribute) {
                if (!$em->contains($newAttribute)) { 
                    $repository->save($newAttribute);
                }
            }
            $this->addFlash('success', 'This setting was updated successfully');
            $em->flush();
        }
        return $this->render('config/attribute-form.html.twig', [
            'attributeForm' => $issueAtrributeForm->createView(),
            'name' => $name
        ]);
    }
    #[Route('/config/status', name: 'app_config_status')]
    public function editStatus(Request $request, EntityManagerInterface $em, StatusRepository $statusRepository): Response
    {
        return $this->processIssueAttributeEdit($request, $em, $statusRepository, 'Status');
    }
    #[Route('/config/priority', name: 'app_config_priority')]
    public function editPriority(Request $request, EntityManagerInterface $em, PriorityRepository $priorityRepository): Response
    {
        return $this->processIssueAttributeEdit($request, $em, $priorityRepository, 'Priority');
    }

    // --- Enable/disabling user ---
    #[Route('/config/disableuser', name: 'app_config_disableuser')]
    public function disableUser(Request $request, UserRepository $userRepository)
    {
        $disableForm = $this->createForm(UserRoleFormType::class);
        $disableForm->handleRequest($request);
        if ($disableForm->isSubmitted() && $disableForm->isValid()) {   
            $user = $disableForm->get('user')->getData();
            $roles = $user->getRoles();
            if (in_array('ROLE_DISABLED', $roles)) {
                $disableForm->get('user')->addError(new FormError('User is already disabled'));
            }
            else {
                $roles[] = 'ROLE_DISABLED';
                $user->setRoles($roles);
                $userRepository->save($user, true);
                $this->addFlash('success', 'User was disabled');
            }
        }
        return $this->render('config/userrole-form.html.twig', [
            'roleForm' => $disableForm->createView(),
            'actionText' => 'Disable user'
        ]);
    }
    #[Route('/config/enableuser', name: 'app_config_enableuser')]
    public function enableUser(Request $request, UserRepository $userRepository)
    {
        $enableForm = $this->createForm(UserRoleFormType::class);
        $enableForm->handleRequest($request);
        if ($enableForm->isSubmitted() && $enableForm->isValid()) {   
            $user = $enableForm->get('user')->getData();
            $roles = $user->getRoles();
            if (!in_array('ROLE_DISABLED', $roles)) {
                $enableForm->get('user')->addError(new FormError('User is already enabled'));
            }
            else {
                $roles = array_diff($roles, ['ROLE_DISABLED']);
                $user->setRoles($roles);
                $userRepository->save($user, true);
                $this->addFlash('success', 'User was enabled');
            }
        }
        return $this->render('config/userrole-form.html.twig', [
            'roleForm' => $enableForm->createView(),
            'actionText' => 'Enable user'
        ]);
    }

    // --- Adding/removing administrator ---
    #[Route('/config/addadmin', name: 'app_config_addadmin')]
    public function addAdministrator(Request $request, UserRepository $userRepository)
    {
        $addAdminForm = $this->createForm(UserRoleFormType::class);
        $addAdminForm->handleRequest($request);
        if ($addAdminForm->isSubmitted() && $addAdminForm->isValid()) {   
            $user = $addAdminForm->get('user')->getData();
            $roles = $user->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                $addAdminForm->get('user')->addError(new FormError('User is already an admin'));
            }
            else {
                $roles[] = 'ROLE_ADMIN';
                $user->setRoles($roles);
                $userRepository->save($user, true);
                $this->addFlash('success', 'User was made admin');
            }
        }
        return $this->render('config/userrole-form.html.twig', [
            'roleForm' => $addAdminForm->createView(),
            'actionText' => 'Add admin'
        ]);
    }
    #[Route('/config/removeadmin', name: 'app_config_removeadmin')]
    public function removeAdministrator(Request $request, UserRepository $userRepository)
    {
        $removeAdminForm = $this->createForm(UserRoleFormType::class);
        $removeAdminForm->handleRequest($request);
        if ($removeAdminForm->isSubmitted() && $removeAdminForm->isValid()) {   
            $user = $removeAdminForm->get('user')->getData();
            $roles = $user->getRoles();
            if (!in_array('ROLE_ADMIN', $roles)) {
                $removeAdminForm->get('user')->addError(new FormError('User is not an administrator'));
            }
            else {
                $roles = array_diff($roles, ['ROLE_ADMIN']);
                $user->setRoles($roles);
                $userRepository->save($user, true);
                $this->addFlash('success', 'User was removed as an admin');
            }
        }
        return $this->render('config/userrole-form.html.twig', [
            'roleForm' => $removeAdminForm->createView(),
            'actionText' => 'Remove admin'
        ]);
    }
}