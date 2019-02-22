<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/users")
     */
    public function getAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if ($users === null) {
            return new View("There are no users", Response::HTTP_NOT_FOUND);
        }
        return $users;
    }

    /**
     * @Rest\Get("/user/{id}")
     */
    public function idAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($user === NULL) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @Rest\Post("/user")
     */
    public function createAction(Request $request)
    {

        $name = $request->request->get('name');
        $email = $request->get('email');
        if(empty($name) || empty($email)) {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new View("Wrong Email format", Response::HTTP_NOT_ACCEPTABLE);
        }

        $user = new User();
        $user->setName($name)
            ->setEmail($email);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return new View("User Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/user/{id}")
     */
    public function updateAction($id,Request $request)
    {
        $name = $request->get('name');
        $email = $request->get('email');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        } elseif(!empty($name) && !empty($email)){
            $user->setName($name)
                ->setEmail($email);
            $em->flush();
            return new View("User Updated Successfully", Response::HTTP_OK);
        } elseif(empty($name) && !empty($email)){
            $user->setEmail($email);
            $em->flush();
            return new View("User Email Updated Successfully", Response::HTTP_OK);
        } elseif(!empty($name) && empty($email)){
            $user->setName($name);
            $em->flush();
            return new View("User Name Updated Successfully", Response::HTTP_OK);
        }

        return new View("User name or role cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/user/{id}")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return new View("deleted successfully", Response::HTTP_OK);
    }
}