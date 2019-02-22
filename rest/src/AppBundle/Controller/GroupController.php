<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/groups")
     */
    public function getAction()
    {
        $groups = $this->getDoctrine()->getRepository('AppBundle:Group')->findAll();
        if ($groups === null) {
            return new View("there are no groups exist", Response::HTTP_NOT_FOUND);
        }
        return $groups;
    }

    /**
     * @Rest\Get("/group/{id}")
     */
    public function idAction($id)
    {
        $group = $this->getDoctrine()->getRepository('AppBundle:Group')->find($id);
        if ($group === NULL) {
            return new View("Group not found", Response::HTTP_NOT_FOUND);
        }

        return $group;
    }

    /**
     * @Rest\Post("/group")
     */
    public function createAction(Request $request)
    {

        $name = $request->request->get('name');
        if(empty($name)) {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }

        $group = new Group();
        $group->setName($name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();
        return new View("Group Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/group/{id}")
     */
    public function updateAction($id,Request $request)
    {
        $name = $request->get('name');
        $em = $this->getDoctrine()->getManager();
        $group = $this->getDoctrine()->getRepository('AppBundle:Group')->find($id);
        if (empty($group)) {
            return new View("Group not found", Response::HTTP_NOT_FOUND);
        } elseif(!empty($name)){
            $group->setName($name);
            $em->flush();
            return new View("Group Updated Successfully", Response::HTTP_OK);
        }

        return new View("Group name cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/group/{id}")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $this->getDoctrine()->getRepository('AppBundle:Group')->find($id);
        if (empty($group)) {
            return new View("Group not found", Response::HTTP_NOT_FOUND);
        }

        $em->remove($group);
        $em->flush();

        return new View("deleted successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/group/{groupId}/user-add/{userId}")
     */
    public function addUserAction($groupId, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Group $group */
        $group = $this->getDoctrine()->getRepository('AppBundle:Group')->find($groupId);
        if (empty($group)) {
            return new View("Group not found", Response::HTTP_NOT_FOUND);
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);
        if (empty($user)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        }

        if ($group->getUsers()->contains($user)) {
            return new View("This user is already in the group.", Response::HTTP_NOT_ACCEPTABLE);
        }

        $group->addUser($user);
        $em->flush();
        

        return new View("User was added to group", Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/group/{groupId}/user-remove/{userId}")
     */
    public function removeUserAction($groupId, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Group $group */
        $group = $this->getDoctrine()->getRepository('AppBundle:Group')->find($groupId);
        if (empty($group)) {
            return new View("Group not found", Response::HTTP_NOT_FOUND);
        }
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);
        if (empty($user)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        }

        if (!$group->getUsers()->contains($user)) {
            return new View("This user isn't in the group", Response::HTTP_NOT_ACCEPTABLE);
        }

//        return new View("User was removed from group", Response::HTTP_OK);

        $group->removeUser($user);
        $em->flush();


        return new View("User was removed from group", Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/group/{groupId}/users")
     */
    public function getUsersAction($groupId)
    {
        $group = $this->getDoctrine()->getRepository('AppBundle:Group')->find($groupId);
        if ($group === null) {
            return new View("Group not found", Response::HTTP_NOT_FOUND);
        }
        return $group->getUsers();
    }
}