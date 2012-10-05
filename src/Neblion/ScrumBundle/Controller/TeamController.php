<?php

namespace Neblion\ScrumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Neblion\ScrumBundle\Entity\Team;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Team controller.
 *
 * @Route("/team")
 */
class TeamController extends Controller
{
    
    /**
     * Finds and displays a Team entity for a project (id = project.id).
     
     * * All members could display the team.
     *
     * @Route("/{id}/show", name="team_show")
     * @Template()
     * @param integer $id Project id
     */
    public function showAction($id)
    {
        // Check if user is authorized
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('NeblionScrumBundle:Project')->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }
        if (!$project->getTeam()) {
            throw $this->createNotFoundException('Unable to find Team entity.');
        }
        
        // Check if user is really a member of this project
        $member = $em->getRepository('NeblionScrumBundle:Member')
                ->isMemberOfProject($user->getId(), $project->getId());
        if (!$member) {
            throw new AccessDeniedException();
        }
        
        $admin = false;
        $members = $membersDisabled = array();
        
        if ($member->getStatus()->getId() == 1) {
            $members[] = $member;
        } else {
            $membersDisabled = array();
            if ($member->getAdmin()) {
                $admin = true;
                // Get Members of the team
                $membersDisabled = $em->getRepository('NeblionScrumBundle:Member')
                    ->getTeamMembersNotEnabled($project->getTeam()->getId());
            }

            // Get Members of the team
            $members = $em->getRepository('NeblionScrumBundle:Member')
                    ->getTeamMembers($project->getTeam()->getId());
        }
        
        /*
        $query = $em->getRepository('NeblionScrumBundle:Member')
                ->getTeamMembers($project->getTeam()->getId(), true);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );
        */
        return array(
            'project'           => $project,
            'team'              => $project->getTeam(),
            //'pagination'    => $pagination,
            'members'           => $members,
            'membersDisabled'   => $membersDisabled,
            'admin'             => $admin,
        );
    }

}
