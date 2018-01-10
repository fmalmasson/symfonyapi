<?php
/**
 * Created by PhpStorm.
 * User: frup72024
 * Date: 10/01/2018
 * Time: 16:21
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/users")
     */
    public function getAction()
    {
        $result = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            if ($result === null) {
                return new View("there are no users", Response::HTTP_NOT_FOUND);
            }

            return $result;
    }

    /**
     * @Rest\Get("/users/{id}")
     */
    public function idAction($id)
    {
        $result = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($result === null) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    /**
     * @Rest\Post("/users")
     *
     */
    public function postAction(Request $request)
    {
        $data = new User();
        $name = $request->get('name');
        $role = $request->get('role');
        if (empty($name) || empty($role)) {
            return new View("Null values are not allowed", Response::HTTP_NOT_ACCEPTABLE);
        }

        $data->setName($name);
        $data->setRole($role);

        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();

        return new View("User added sucessfully", Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/users/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $data = new User();
        $name = $request->get('name');
        $role = $request->get('role');

        $em = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }

        elseif (!empty($name) && !empty($role)) {
            $user->setName($name);
            $user->setRole($role);
            $em->flush();

            return new View("user updated successfully", Response::HTTP_OK);
        }
        elseif (empty($name) && !empty($role)) {
            $user->setRole($role);
            $em->flush();

            return new View("Role updated successfully", Response::HTTP_OK);
        }
        elseif (!empty($name) && empty($role)) {
            $user->setName($name);
            $em->flush();

            return new View("Name updated successfully", Response::HTTP_OK);
        }

        else return new View("User name and role cant be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/users/{id}")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if (empty($user)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        }
        else {
            $em->remove($user);
            $em->flush();
        }

        return new View('User deleted successfully', Response::HTTP_OK);
    }
}