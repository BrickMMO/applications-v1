<?php

if(!security_is_logged_in())
{
    $data = array('message' => 'Must be logged in to use this ajax call.', 'error' => false);
    return;
}

/*
elseif(!isset($_user['github_access_token']))
{
    $data = array('message' => 'Admin user must approve BrickMMO GitHub app.', 'error' => false);
    return;
}
*/

$github = setting_fetch('GITHUB_ACCESS_TOKEN');
$url = 'https://api.github.com/users/'.$_GET['key'];

$headers[] = 'Content-type: application/json';
$headers[] = 'Authorization: Bearer '.$github;
$headers[] = 'User-Agent: Awesome-Octocat-App';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$user = json_decode(curl_exec($ch), true);

curl_close($ch);

$headers[] = 'Content-type: application/json';
// $headers[] = 'Authorization: Bearer '.$user['github_access_token'];
// $headers[] = 'User-Agent: Awesome-Octocat-App   ';

$repos = array();

for($i = 0; $i < $user['public_repos'] / 100; $i ++)
{

    $url = 'https://api.github.com/users/'.$_GET['key'].'/repos?per_page=100&page='.($i+1);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $repos = array_merge($repos, json_decode(curl_exec($ch), true));

    curl_close($ch);

}

$data = array(
    'message' => 'GitHub account and repo list has been retrieved.',
    'error' => false, 
    'user' => $_user,
    'repos' => $repos,
);
