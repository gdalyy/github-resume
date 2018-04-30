<?php

namespace Exozet\ChallengeBundle\Controller;

use Exozet\ChallengeBundle\Services\GitHubService;
use Exozet\ChallengeBundle\Utils\Tools;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * GithubResumeController
 *
 * @author Ghaith Daly <https://www.linkedin.com/in/ghaith-daly-352006152/>
 */
class GitHubResumeController extends Controller
{
    /**
     * Landing page action for GitHub Resumé
     *
     * @Route("/", name="landing_page")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // creating the form 
        $defaultData = ['message' => 'GitHub Resumé search form'];
        $form = $this->createFormBuilder($defaultData)
            ->add('username', TextType::class, [
                'required' => true
            ])
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // redirecting to search action
            return $this->redirectToRoute('search_user', ['username' => $data['username']]);

        }

        // rendering the template
        return $this->render('ExozetChallengeBundle:Challenge:index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Search action for GitHub user
     *
     * @Route("/resume/{username}", name="search_user")
     * @param $username
     * @param GitHubService $gs
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction($username, GitHubService $gs)
    {
        // get github user profile data
        $responseUser = $gs->getUser($username);
        // if error render error template
        if (array_key_exists('code', $responseUser))
            return $this->render('ExozetChallengeBundle:Challenge:failure.html.twig', ['response' => $responseUser]);

        // get the user repositories
        $responseRepos = $gs->getRepos($username);

        // get user programming languages
        $languages = array();
        $lines = null;
        if (isset($responseRepos)) {

            // constructing languages array
            foreach ($responseRepos as $repo) {
                $responseLanguages = $gs->getRepoMainLanguage($repo->languages_url);

                if (array_key_exists($repo->language, $languages))
                    $languages[$repo->language]['lines'] += current($responseLanguages);
                else
                    $languages[$repo->language]['lines'] = current($responseLanguages);

                // accumulating programming languages lines
                $lines += current($responseLanguages);
            }

            foreach ($languages as &$language) {
                $language['percentage'] = Tools::twoDecimalFloat(($language['lines'] / $lines)*100);
            }

            // sorting languages array based on percentage DESC
            uasort($languages, [Tools::class, 'sortByPercentage']);
        }

        // rendering the template
        return $this->render('ExozetChallengeBundle:Challenge:resume.html.twig', [
            'user' => $responseUser,
            'repos' => $responseRepos,
            'languages' => $languages,
        ]);
    }
}
