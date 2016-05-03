<?php
/**
 * Created by PhpStorm.
 * User: jannatul
 * Date: 23/04/16
 * Time: 00:06
 */

namespace Reviewer\ApiBundle\Controller;

use Reviewer\ReviewBundle\Entity\Review;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class ReviewController extends FOSRestController
{
    public function getReviewsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $reviewEntry = $em->getRepository('ReviewerReviewBundle:Review')->findAll();

        return $this->handleView($this->view($reviewEntry));
    }

    public function getReviewAction($bookId)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('ReviewerReviewBundle:Review')->getAllReviews($bookId);
        if(!$entry) {
            $view = $this->view(null, 404);
        } else {
            $view = $this->view($entry);
        }

        return $this->handleView($view);
    }

    public function postReviewAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $book = $em->getRepository('ReviewerReviewBundle:Book')->find($id);
        $userReview = $em->getRepository('ReviewerReviewBundle:Review')->checkUserReview($id,$user);

        // if a user has already reviewed this book
        if ($userReview) {
            return $this->handleView($this->view(null, 400));
        }


        $reviewEntry = new Review();
        $form = $this->createForm(new ReviewType(), $reviewEntry);
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $reviewEntry->setAuthor($this->getUser());
            $reviewEntry->setVotes(0);
            $reviewEntry->setBook($book);
            $em->persist($reviewEntry);
            $em->flush();

            return $this->handleView($this->view(null, 201)
                ->setLocation(
                    $this->generateUrl('api_review_get_review',
                        ['id' => $reviewEntry->getId()]
                    )
                )
            );
        } else {
            return $this->handleView($this->view($form, 400));
        }
    }

    public function putReviewAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $reviewEntry = $em->getRepository('ReviewerReviewBundle:Review')->getAllReviews($id);

        $form = $this->createForm(new ReviewType(), $reviewEntry);
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));
        // Point 2 of list above
        if($form->isValid()) {
            $em->persist($reviewEntry);
            $em->flush();
            // set status code to 201 and set the Location header
            return $this->handleView($this->view(null, 201)
                ->setLocation(
                    $this->generateUrl('api_review_get_review',
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

    public function deleteReviewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('ReviewerReviewBundle:review')->find($id);
        if(!$entry) {
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // and the status code defaults to 200 "OK"
            $em->remove($entry);
            $em->flush();

            $view = $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return $this->handleView($view);
    }
}