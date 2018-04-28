<?php

namespace Exozet\ChallengeBundle\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * GitHubService : GitHub API 
 *
 * @author Ghaith Daly <https://www.linkedin.com/in/ghaith-daly-352006152/>
 */
class GitHubService
{
    const GITHUB_API_PREFIX = 'https://api.github.com/users/';
    const GITHUB_ACCESS_TOKEN = '7103ca0fcd71f0049465f06f2aaf86fcc3bc1ba9';

    private $client;

    /**
     * GitHubService constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * get GitHub user profile data
     * 
     * @param $username
     * @return array|mixed
     */
    public function getUser($username)
    {
        $response = null;

        try {

            $response = $this->client->request('GET', self::GITHUB_API_PREFIX . $username, [
                'query' => [
                    'access_token' => self::GITHUB_ACCESS_TOKEN,
                ]
            ]);

        } catch (ConnectException $e) {
            return ['code' => 500, 'message' => 'Oops! We encountered an unexpected error. please try again',];
        } catch (RequestException $e) {
            return ['code' => 404, 'message' => 'Oops! User not found',];
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * get GitHub user repositories data
     * 
     * @param $username
     * @return mixed
     */
    public function getRepos($username)
    {

        $response = $this->client->request('GET', self::GITHUB_API_PREFIX . $username . '/repos', [
            'query' => [
                'access_token' => self::GITHUB_ACCESS_TOKEN,
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * get repository main languages lines count
     * 
     * @param $url
     * @return mixed
     */
    public function getRepoMainLanguage($url)
    {
        $response = $this->client->request('GET', $url, [
            'query' => [
                'access_token' => self::GITHUB_ACCESS_TOKEN,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}