<?php

namespace GitlabReporter\Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class GuzzleClient implements GitlabInterface
{
    const BASE_URL = 'https://gitlab.com/api/v4/';
    const ACCESS_TOKEN = 'yb1mCqQKZqiihQ2MJ9yq';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $guzzleOptions = [
        'timeout' => 60,
    ];

    public function __construct(string $accessToken)
    {
        $this->client = new Client(
            array_merge(
                [
                    'base_uri' => self::BASE_URL,
                    'headers' => [
                        //'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Private-Token' => $accessToken,
                    ],
                ],
                $this->guzzleOptions
            )
        );
    }

    public function getMergeRequestFromBranch(string $project, string $branch): array
    {
        /** @var  $response */
        $response = $this->client->request('GET', 'projects/' . urlencode($project) . '/merge_requests?state=opened');

        $mergeRequests = json_decode($response->getBody()->getContents(), true);

        foreach ($mergeRequests as $mergeRequest) {
            if ($branch === $mergeRequest['source_branch']) {
                return $mergeRequest;
            }
        }
    }

    public function postCommentToMergeRequest(string $project, int $mergeRequestIid, string $body)
    {
        $this->client->request(
            'POST',
            'projects/' . urlencode($project) . '/merge_requests/' . $mergeRequestIid . '/notes',
            [
                'form_params' => [
                    'body' => $body,
                ],
            ]
        );

    }
}
