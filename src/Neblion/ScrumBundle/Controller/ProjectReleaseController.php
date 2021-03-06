<?php

namespace Neblion\ScrumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Neblion\ScrumBundle\Entity\ProjectRelease;
use Neblion\ScrumBundle\Form\ProjectReleaseType;
use Symfony\Component\Form\FormError;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * ProjectRelease controller.
 *
 * @Route("/release")
 */
class ProjectReleaseController extends Controller
{
    /**
     * Lists all ProjectRelease entities.
     *
     * @Route("/{id}", name="release_list")
     * @Template()
     */
    public function indexAction($id)
    {
        // Check if user is authorized
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        // Load project
        $project = $em->getRepository('NeblionScrumBundle:Project')->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }
        
        // Check if user is really a member of this project
        $member = $em->getRepository('NeblionScrumBundle:Member')
                ->isMemberOfProject($user->getId(), $project->getId());
        if (!$member) {
            throw new AccessDeniedException();
        }
        
        $releases = $em->getRepository('NeblionScrumBundle:ProjectRelease')
                ->getListForProject($project);
        
        return array(
            'project'       => $project,
            'releases'      => $releases,
        );
    }

    /**
     * Displays a form to create a new ProjectRelease entity.
     * 
     * Only PO, SM and admin could add new release.
     * You could not add a new release if a release with no due date exists.
     *
     * @Route("/{id}/new", name="release_new")
     * @Template()
     */
    public function newAction($id)
    {
        // Check if user is authorized
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();

        // Load project
        $project = $em->getRepository('NeblionScrumBundle:Project')->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }
        
        // Check if user is really a member of this project and if he has right role
        $member = $em->getRepository('NeblionScrumBundle:Member')
                ->isMemberOfProject($user->getId(), $project->getId());
        if (!$member or !in_array($member->getRole()->getId(), array(1, 2))) {
            if (!$member->getAdmin()) {
                throw new AccessDeniedException();
            }
        }
        
        // Check if there is a release with no due date
        $release = $em->getRepository('NeblionScrumBundle:ProjectRelease')
                ->hasReleaseWithNoDueDate($project->getId());
        if ($release) {
            // Set flash message
            $this->get('session')->getFlashBag()->add('notice', 'A release with no due date is in progress, you could not create a new one!');
            return $this->redirect($this->generateUrl('release_list', array('id' => $project->getId())));
        }
        
        $release = new ProjectRelease();
        $form   = $this->createForm(new ProjectReleaseType(), $release);

        return array(
            'project'   => $project,
            'release'   => $release,
            'form'      => $form->createView()
        );
    }

    /**
     * Creates a new ProjectRelease entity.
     *
     * @Route("/{id}/create", name="release_create")
     * @Method("post")
     * @Template("NeblionScrumBundle:ProjectRelease:new.html.twig")
     */
    public function createAction($id)
    {
        // Check if user is authorized
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();

        // Load project
        $project = $em->getRepository('NeblionScrumBundle:Project')->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }
        
        // Check if user is really a member of this project and if he has right role
        $member = $em->getRepository('NeblionScrumBundle:Member')
                ->isMemberOfProject($user->getId(), $project->getId());
        if (!$member or !in_array($member->getRole()->getId(), array(1, 2))) {
            if (!$member->getAdmin()) {
                throw new AccessDeniedException();
            }
        }
        
        // Load process status
        $status = $em->getRepository('NeblionScrumBundle:ProcessStatus')->find(1);
        
        $release = new ProjectRelease();
        $release->setProject($project);
        $release->setStatus($status);
        
        $request = $this->getRequest();
        $form    = $this->createForm(new ProjectReleaseType(), $release);
        $form->bindRequest($request);
        
        // Check if ther is a release with no due date
        $hasReleaseWithNoDueDate = $em->getRepository('NeblionScrumBundle:ProjectRelease')
                ->hasReleaseWithNoDueDate($project->getId());
        if ($hasReleaseWithNoDueDate) {
            $form->addError(new FormError('A release with no due date is in progress, you could not create a new one!'));
        }
        
        // Check if the release does not cover an other release
        $coveringRelease = $em->getRepository('NeblionScrumBundle:ProjectRelease')
                ->isCoveringRelease($project->getId(), $release->getStart(), $release->getEnd());
        if ($coveringRelease) {
            $form->addError(new FormError('The release\'s dates overlap (' . $coveringRelease->getName() . ')'));
        }

        if ($form->isValid()) {
            $em->persist($release);
            
            // store activity            
            $this->get('scrum_activity')->add($project, $user, 'create release ' . $release->getName(), 
                    $this->generateUrl('release_list', array('id' => $project->getId())),
                    'Project #' . $project->getId() . '  releases');
            
            $em->flush();

            // Set flash message
            $this->get('session')->getFlashBag()->add('success', 'Release was created with success!');
            return $this->redirect($this->generateUrl('release_list', array('id' => $project->getId())));
        }

        return array(
            'project'   => $project,
            'release'   => $release,
            'form'      => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing ProjectRelease entity.
     *
     * @Route("/{id}/edit", name="release_edit")
     * @Template()
     */
    public function editAction($id)
    {
        // Check if user is authorized
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        $release = $em->getRepository('NeblionScrumBundle:ProjectRelease')->find($id);
        if (!$release) {
            throw $this->createNotFoundException('Unable to find Release entity.');
        }
        
        // Check if user is really a member of this project and if he has right role
        $member = $em->getRepository('NeblionScrumBundle:Member')
                ->isMemberOfProject($user->getId(), $release->getProject()->getId());
        if (!$member or !in_array($member->getRole()->getId(), array(1, 2))) {
            if (!$member->getAdmin()) {
                throw new AccessDeniedException();
            }
        }
        
        $form = $this->createForm(new ProjectReleaseType(), $release);

        return array(
            'project'   => $release->getProject(),
            'release'   => $release,
            'form'      => $form->createView(),
        );
    }

    /**
     * Edits an existing ProjectRelease entity.
     *
     * @Route("/{id}/update", name="release_update")
     * @Method("post")
     * @Template("NeblionScrumBundle:ProjectRelease:edit.html.twig")
     */
    public function updateAction($id)
    {
        // Check if user is authorized
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        // Load the release
        $release = $em->getRepository('NeblionScrumBundle:ProjectRelease')->find($id);
        if (!$release) {
            throw $this->createNotFoundException('Unable to find Release entity.');
        }
        
        // Check if user is really a member of this project and if he has right role
        $member = $em->getRepository('NeblionScrumBundle:Member')
                ->isMemberOfProject($user->getId(), $release->getProject()->getId());
        if (!$member or !in_array($member->getRole()->getId(), array(1, 2))) {
            if (!$member->getAdmin()) {
                throw new AccessDeniedException();
            }
        }
        
        $form   = $this->createForm(new ProjectReleaseType(), $release);
        $request = $this->getRequest();
        $form->bindRequest($request);

        if ($form->isValid()) {
            // Update end date if it is null
            // ToDO: add a test validation
            if ($release->getStatus()->getId() == 3 and is_null($release->getEnd())) {
                $release->setEnd(new \DateTime());
            }
            
            // store activity            
            $this->get('scrum_activity')->add($release->getProject(), $user, 'update release', 
                    $this->generateUrl('release_edit', array('id' => $release->getId())),
                    'Release #' . $release->getId());
            
            $em->flush();

            // Set flash message
            $this->get('session')->getFlashBag()->add('success', 'Release was updated with success!');
            return $this->redirect($this->generateUrl('release_list', array('id' => $release->getProject()->getId())));
        }

        return array(
            'project'   => $release->getProject(),
            'release'   => $release,
            'form'      => $form->createView(),
        );
    }

    /**
     * Deletes a ProjectRelease entity.
     *
     * @Route("/{id}/delete", name="release_delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        // Check if user is authorized
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();

        $release = $em->getRepository('NeblionScrumBundle:ProjectRelease')->find($id);
        if (!$release) {
            throw $this->createNotFoundException('Unable to find Release entity.');
        }
        
        // Check if user is really a member of this project and if he has right role
        $member = $em->getRepository('NeblionScrumBundle:Member')
                ->isMemberOfProject($user->getId(), $release->getProject()->getId());
        if (!$member or !in_array($member->getRole()->getId(), array(1, 2))) {
            if (!$member->getAdmin()) {
                throw new AccessDeniedException();
            }
        }
        
        // Check if there is a sprint attached to this release
        $sprints = $em->getRepository('NeblionScrumBundle:Sprint')->getForRelease($release->getId());
        if ($sprints) {
            // Set flash message
            $this->get('session')->getFlashBag()->add('error', 'Impossible to delete this release, there is at least a sprint attached to it!');
            return $this->redirect($this->generateUrl('release_list', array('id' => $release->getProject()->getId())));
        }
        
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em->remove($release);
                
                // store activity            
                $this->get('scrum_activity')->add($release->getProject(), $user, 'delete release ' . $release->getName(), 
                    $this->generateUrl('release_list', array('id' => $release->getProject()->getId())),
                    'Project #' . $release->getProject()->getId() . ' releases');
                
                $em->flush();
                
                // Set flash message
                $this->get('session')->getFlashBag()->add('success', 'Release was deleted with success!');
                return $this->redirect($this->generateUrl('release_list', array('id' => $release->getProject()->getId())));
            }
        }

        return array(
            'project'   => $release->getProject(),
            'release'   => $release,
            'form'      => $form->createView(),
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
