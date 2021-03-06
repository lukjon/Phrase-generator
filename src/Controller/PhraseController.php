<?php


namespace App\Controller;


use App\Entity\Phrase;
use App\Service\PhraseGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PhraseController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function homepage()
    {

        $sentences_format = '
%jaustukai tu %adjective , %adjective , %adjective %noun %sentence_ending
%jaustukai tu %adjective %noun %sentence_ending
Kad tave %nouns %verb %sentence_ending
Cit %noun_list %sentence_ending
';

// Firstline: variable name, second line:elements
        $sentence_variables = 'adjective
%adjective_list;%adjective_list , %adjective_list; %adjective_color %adjective_list

adjective_list
didelė;milžiniška;aukštielnika;skraidanti;išverstaakė;raudona;protinga;stora;nuobodi;kvanka;beprotė;nutrūktgalvė;ėdrūnė;užknisanti;stipri

adjective_color
žalia;raudona;geltona;rožinė;mėlyna

noun
%noun_list;%adjective %noun_list

noun_list
karvė;rupūžė;beždionė;makaka;musė;avinė;varna;varla

nouns
velniai;katės;plaštakės;varnos;žiūrkės;žuvys

jaustukai
Ai;Ai;Oj;Oooj;Ooj;Bam;Bum;Pyst;Po šimts perkūnų;Po šimts pypkių;Blemba;Velnias;Eina namas;

verb
pagautų;nuneštų;sulestų;pakastų;pamaitintų;apšviestų;atraugėtų;apmurktų;žaibs nutrenktų

love_or_hate
myliu;nekenčiu;dievinu;patinka

sentence_ending
.;!;!!;!?;*lol*
';

        $generator = new PhraseGenerator();
        $sentence = $generator->generate($sentences_format, $sentence_variables);

        return $this->render('phrase/homepage.html.twig',
            [
                'phrase' => $sentence
            ]);
    }

    /**
     * @Route("/phrases/new", name="app_phrase_create", methods="POST")
     * @param EntityManagerInterface $entityManager
     * @throws \Exception
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $text = $request->request->get('phrase_text');

        $phrase = new Phrase();
        $phrase->setText($text);
        $phrase->setSlug('phrase-'.rand(0,5000000));
        $phrase->setCreatedAt(new \DateTime());

        $entityManager->persist($phrase);
        $entityManager->flush();

        return $this->redirectToRoute('app_phrases',['slug' => $phrase->getSlug()]);
    }

    /**
     * @Route("/phrases/{slug}", name="app_phrases")
     * @param $slug
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($slug, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Phrase::class);
        /** @var Phrase|null $phrase */
        $phrase = $repository->findOneBy(['slug' => $slug]);
        if (!$phrase)
        {
            throw $this->createNotFoundException(sprintf("Phrase not found"));
        }

        return $this->render('phrase/show.html.twig', [
            'phrase' => $phrase->getText(),
            'phraseDate' => $phrase->getCreatedAt()
        ]);
    }
}