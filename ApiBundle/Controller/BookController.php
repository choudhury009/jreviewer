<?php
/**
 * Created by PhpStorm.
 * User: jannatul
 * Date: 23/03/16
 * Time: 00:10
 */

namespace Reviewer\ApiBundle\Controller;

use Reviewer\ReviewBundle\Entity\Book;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class BookController extends FOSRestController
{
//853627655932-47c7pb7kpc8o4242i2nihtbr1fogbukv.apps.googleusercontent.com
//ajS5uciQklkJA_WEMDu8kfx1
    public function getBooksAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('ReviewerReviewBundle:Book')->findAll();

        return $this->handleView($this->view($entries));
    }

    public function getBookAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('ReviewerReviewBundle:Book')->find($id);
        if(!$entry) {
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // and the status code defaults to 200 "OK"
            $view = $this->view($entry);
        }

        return $this->handleView($view);
    }

    public function postBookAction(Request $request)
    {
        $bookEntry = new Book();
        $form = $this->createForm(new BookType(), $bookEntry);
        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));
        // Point 2 of list above
        if($form->isValid()) {
            // Point 4 of list above
            $em = $this->getDoctrine()->getManager();
            $bookEntry->setAuthor($this->getUser());
            $bookEntry->setTimestamp(new \DateTime());
            $em->persist($bookEntry);
            $em->flush();
            // set status code to 201 and set the Location header
            return $this->handleView($this->view(null, 201)
                ->setLocation(
                    $this->generateUrl('api_book_get_book',
                        ['id' => $bookEntry->getId()]
                    )
                )
            );
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }

    public function putBookAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $bookEntry = $em->getRepository('ReviewerReviewBundle:Book')->find($id);

        // create if statement to check if book exists!!!!

        $form = $this->createForm(new BookType(), $bookEntry);
        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));
        // Point 2 of list above
        if($form->isValid()) {
            $em->persist($bookEntry);
            $em->flush();
            // set status code to 201 and set the Location header
            return $this->handleView($this->view(null, 201)
                ->setLocation(
                    $this->generateUrl('api_book_get_book',
                        ['id' => $id]
                    )
                )
            );
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }

    public function deleteBookAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('ReviewerReviewBundle:Book')->find($id);
        if(!$entry) {
            // no book entry is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            $em->remove($entry);
            $em->flush();

            $view = $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return $this->handleView($view);
    }
}