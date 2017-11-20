<?php

namespace GitlabReporter\Client;

interface GitlabInterface
{
    public function getMergeRequestFromBranch(string $project, string $branch);

    public function postCommentToMergeRequest(string $project, int $mergeRequestId, string $body);
}
