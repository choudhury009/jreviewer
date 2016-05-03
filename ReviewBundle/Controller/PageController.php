<?php

namespace Reviewer\ReviewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
class PageController extends Controller
{
    /**
     * this is the home page where is will list latest 9 books
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ReviewerReviewBundle:Book')->getAllBooksQuery();

        $paginator  = $this->get('knp_paginator');
        $bookEntries = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $count = count($bookEntries);

        return $this->render('ReviewerReviewBundle:Page:index.html.twig',
            ['bookentries' => $bookEntries, 'count' => $count]);
    }

    public function aboutAction()
    {
        return $this->render('ReviewerReviewBundle:Page:about.html.twig');
    }

    public function contactAction()
    {
        return $this->render('ReviewerReviewBundle:Page:contact.html.twig');
    }

    /**
     * user will be able to search all books by title and author.
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $searchStr = $data['search'];
        if (isset($_POST["gSearch"])) {
            $client = new Client();
            $req = $client->request('GET', 'https://www.googleapis.com/books/v1/volumes?q=intitle:' .$searchStr, []);
            $decode = json_decode($req->getBody());
            $total = $decode->totalItems;
            $books = null;
            if ($total != 0) {
                $books = $decode->items;
            }

            return $this->render('ReviewerReviewBundle:Page:googleBook.html.twig',
                ['books' => $books]);
        }

        $searchQuery = $em->getRepository('ReviewerReviewBundle:Book')->search($searchStr);
        $paginator  = $this->get('knp_paginator');
        $books = $paginator->paginate(
            $searchQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $count = count($books);

        return $this->render('ReviewerReviewBundle:Page:index.html.twig',
            ['bookentries' => $books, 'count' => $count]);
    }
}
