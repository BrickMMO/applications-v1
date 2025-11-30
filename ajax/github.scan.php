<?php

// TODO
// If not logged in
// Check for account and repo

if(!security_is_logged_in())
{
    $data = array('message' => 'Must be logged in to use this ajax call.', 'error' => false);
    return;
}

$id = github_scan_repo($_GET['account'], $_GET['repo']);

// debug_pre($query);
// debug_pre($error_comments);

$query = 'SELECT *
    FROM repos
    WHERE id = "'.$id.'"
    LIMIT 1';
$result = mysqli_query($connect, $query);

$repo = mysqli_fetch_assoc($result);

$query = 'SELECT COUNT(*) AS total_repos
    FROM repos';
$result = mysqli_query($connect, $query);

$record = mysqli_fetch_assoc($result);

setting_update('GITHUB_REPOS_SCANNED', $record['total_repos']);

$data = array(
    'message' => 'GitHub repo details has been retrieved.',
    'error' => false, 
    'repo' => $repo,
    'pull_requests' => $repo['pull_requests'],
    'errors' => explode(chr(13),$repo['error_comments'])
);
