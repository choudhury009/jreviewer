<?php

namespace Reviewer\ReviewBundle\Controller;

use Reviewer\ReviewBundle\Form\ReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Reviewer\ReviewBundle\Entity\Book;
use Reviewer\ReviewBundle\Form\BookType;
use Reviewer\ReviewBundle\Entity\Review;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class ReviewController extends Controller
{
    /**
     * a specific book with all of its reviews
     */
    public function viewAction($id, $msg = false)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $bookEntry = $em->getRepository('ReviewerReviewBundle:Book')->find($id);
        $reviewEntry = $em->getRepository('ReviewerReviewBundle:Review')->getAllReviews($id);
        $review = $em->getRepository('ReviewerReviewBundle:Review')->checkUserReview($id,$user);

        $securityContext = $this->container->get('security.authorization_checker');
        $bookShelf = false;
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $username = $user->getUsername();
            $bookShelf = $em->getRepository('ReviewerReviewBundle:Book')->getShelf($id, $username, true);
        }
        if (isset($_GET['msg'])) {
            $msg = 'Sorry, you have already reviewed this book';
        } elseif (isset($_GET['bookExists'])) {
            $msg = 'Sorry, this book already exists';
        }

        return $this->render('ReviewerReviewBundle:Review:view.html.twig', [
            'book' => $bookEntry,
            'reviews' => $reviewEntry,
            'hasReviewed' => $review,
            'inShelf' => $bookShelf,
            'msg' => $msg,
        ]);
    }

    /**
     * a specific google book details
     * @param Request $request
     * @return Response
     */
    public function googleBookAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
//        $searchStr = $data['search'];
//        if (isset($_POST["gSearch"])) {
            $client = new Client();
            $req = $client->request('GET', 'https://www.googleapis.com/books/v1/volumes?q=id:' . $id);
            $decode = json_decode($req->getBody());
            $total = $decode->totalItems;
            $book = null;
            if ($total != 0) {
                $book = $decode->items;
            }

            return $this->render('ReviewerReviewBundle:Review:viewGoogleBook.html.twig',
                ['book' => $book]);
//        }
    }

    /**
     * this is where the user is able to create a review for a book
     * they will only be allowed to create one review per book
     */
    public function addAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $book = $em->getRepository('ReviewerReviewBundle:Book')->find($id);
        $userReview = $em->getRepository('ReviewerReviewBundle:Review')->checkUserReview($id,$user);

        // if a user has already reviewed this book then redirect them to the book page.
        if ($userReview) {
            return $this->redirect($this->generateUrl('review_view',
                ['id' => $book->getId(), 'msg' => 'review-done']));
        }

        $reviewEntry = new Review();
        $form = $this->createForm(new ReviewType(), $reviewEntry);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $reviewEntry->setAuthor($this->getUser());
            $reviewEntry->setVotes(0);
            $reviewEntry->setBook($book);
            $em->persist($reviewEntry);
            $em->flush();

            return $this->redirect($this->generateUrl('review_view',
                ['id' => $book->getId()]));
        }

        return $this->render('ReviewerReviewBundle:Review:addReview.html.twig',
            ['form' => $form->createView(),'book_name' => $book->getTitle()]);
    }

    /**
     * the user is able to store a specific book in their account
     * this allows them to quickly find the books they want to read in the future
     */
    public function shelfAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('ReviewerReviewBundle:Book')->find($id);
        $username = $this->getUser()->getUsername();
        $bookShelf = $em->getRepository('ReviewerReviewBundle:Book')->getShelf($id, $username);
        $book->setShelf($bookShelf);
        $em->persist($book);
        $em->flush();

        return $this->redirect($this->generateUrl('user_account'));
    }

    /**
     * allows the user to create or add a book from Google Books
     *
     */
    public function createAction(Request $request)
    {
        $data = $request->request->all();
        $searchStr = $data['googleBook'];
        $em = $this->getDoctrine()->getManager();
        if (isset($_POST["insertGoogleBook"])) {
            $client = new Client();
            $req = $client->request('GET', 'https://www.googleapis.com/books/v1/volumes?q=id:' .$searchStr);
            $decode = json_decode($req->getBody());
            $total = $decode->totalItems;
            $books = null;
            if ($total != 0) {
                $books = $decode->items;
                $title = $books[0]->volumeInfo->title;
                $author = $books[0]->volumeInfo->authors[0];
                $description = "no summary";
                $url = $books[0]->volumeInfo->imageLinks->smallThumbnail;
                if(isset($books[0]->volumeInfo->description)) {
                    $description = $books[0]->volumeInfo->description;
                }

                $checkBook = $em->getRepository('ReviewerReviewBundle:Book')->checkIfBookExists($title);
                if ($checkBook) {
                    $bookId = $checkBook;
                    $msg = 'Sorry, this book already exists!';

                    return $this->redirect($this->generateUrl('review_view',
                        ['id' => $bookId, 'bookExists' => $msg]));
                } else {
                    $bookEntry = new Book();
                    $bookEntry->setTitle($title);
                    $bookEntry->setAuthor($author);
                    $bookEntry->setSummary($description);
                    $bookEntry->setReview("no review");
                    $bookEntry->setUrl($url);
                    $bookEntry->setUploader($this->getUser());
                    $bookEntry->setTimestamp(new \DateTime());
                    $em->persist($bookEntry);
                    $em->flush();
                    $bookId = $bookEntry->getId();

                    return $this->redirect($this->generateUrl('review_view', ['id' => $bookId]));
                }

            }
        } else {
            $bookEntry = new Book();
            $form = $this->createForm(new BookType(), $bookEntry,[
                'action' => $request->getUri()
            ]);

            $form->handleRequest($request);
            if($form->isValid()) {
                $title = $bookEntry->getTitle();
                $checkBook = $em->getRepository('ReviewerReviewBundle:Book')->checkIfBookExists($title);
                if ($checkBook) {
                    $msg = 'Sorry, this book already exists!';

                    return $this->render('ReviewerReviewBundle:Review:create.html.twig',
                        ['form' => $form->createView(), 'msg' => $msg]);
                } else {
                    $bookEntry->setUploader($this->getUser());
                    $bookEntry->setTimestamp(new \DateTime());
                    $em->persist($bookEntry);
                    $em->flush();

                    return $this->redirect($this->generateUrl('review_view',
                        ['id' => $bookEntry->getId()]));
                }
            }

            return $this->render('ReviewerReviewBundle:Review:create.html.twig',
                ['form' => $form->createView()]);
        }
    }

    /**
     * user is able to vote up or down once per book
     * they can only like or dislike if they are logged in
     */
    public function voteAction($bookId, $reviewId, $result)
    {
        $em = $this->getDoctrine()->getManager();
        $username = $this->getUser()->getUsername();
        $voters = $em->getRepository('ReviewerReviewBundle:Review')->getVoters($reviewId,$username,$result);
        if ($voters !== false) {
            $reviewObj = $em->getRepository('ReviewerReviewBundle:Review')->find($reviewId);
            $reviewVotes = $em->getRepository('ReviewerReviewBundle:Review')->getVotes($reviewId);
            $hasVotes = $reviewVotes['votes'];
            if ($result == 'up') {
                $hasVotes = $hasVotes+1;
            } elseif ($result == 'down') {
                $hasVotes = $hasVotes-1;
            }
            $reviewObj->setUpVoters($voters['upVoters']);
            $reviewObj->setDownVoters($voters['downVoters']);
            $reviewObj->setVotes($hasVotes);
            $em->persist($reviewObj);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('review_view',
            ['id' => $bookId]));
    }

    /**
     * only an admin is allowed to delete reviews
     * that do not comply with review standards
     */
    public function deleteAction($bookId, $reviewId)
    {
        $em = $this->getDoctrine()->getManager();
        $voters = $em->getRepository('ReviewerReviewBundle:Review')->find($reviewId);
        $em->remove($voters);
        $em->flush();

        return $this->redirect($this->generateUrl('review_view',
            ['id' => $bookId]));
    }

    /**
     * allows the user to edit their review
     */
    public function editAction($bookId, $reviewId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $userReview = $em->getRepository('ReviewerReviewBundle:Review')->find($reviewId);
        $reviewUser = $userReview->getAuthor();
        if ($user == $reviewUser) {
            $book = $em->getRepository('ReviewerReviewBundle:Book')->find($bookId);
            $form = $this->createForm(new ReviewType($em, $userReview->getBook()), $userReview, [
                'action' => $request->getUri()
            ]);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userReview->setAuthor($this->getUser());
                $userReview->setVotes(0);
                $userReview->setBook($book);
                $em->persist($userReview);
                $em->flush();

                return $this->redirect($this->generateUrl('review_view',
                    ['id' => $book->getId()]));
            }

        }

        return $this->render('ReviewerReviewBundle:Review:edit.html.twig', [
            'form' => $form->createView(),
            'book_name' => $book->getTitle(),
            'userId' => $reviewUser->getId()
        ]);
    }
}
