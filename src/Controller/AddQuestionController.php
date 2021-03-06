<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AddQuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddQuestionController extends AbstractController
{
    /**
     * @Route("coach/add/question", name="add_question")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (!in_array("ROLE_COACH", $this->getUser()->getRoles())) {
            return $this->redirectToRoute("profile_page");
        }

        $question = new Question();
        for ($i = 0; $i < 4; $i++) {
            $question->addAnswer(new Answer());
        }


        /**
         * @var Answer $answer
         **/

        $form = $this->createForm(AddQuestionType::class, $question);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();
            foreach ($question->getAnswer() as $answer) {
                if ($answer->getAnswer() == "") {
                    $question->removeAnswer($answer);
                } else {
                    trim($answer->getAnswer());
                }
            }
            $em->persist($question);
            $em->flush();
            return $this->redirectToRoute('view_question');
        }


        return $this->render('add_question/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
