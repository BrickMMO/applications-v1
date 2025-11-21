<?php

include('../../includes/connect.php');
include('../../includes/config.php');
include('../../functions/functions.php');

echo "<h2>Fetching BrickMMO Repositories</h2>";

// GitHub API endpoint for BrickMMO organization repos
$page = 1;
$perPage = 100;
$totalRepos = 0;

while (true) 
{

    $url = 'https://api.github.com/orgs/BrickMMO/repos?per_page='.$perPage.
        '&page='.$page.
        '&client_id='.GITHUB_CLIENT_ID.'&client_secret='.GITHUB_CLIENT_SECRET;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: BrickMMO-Applications',
        'Accept: application/vnd.github+json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response === false || $httpCode !== 200) 
    {

        echo 'Failed to fetch repositories from page '.$page.'<br>';
        break;

    }
    
    $repos = json_decode($response, true);
    
    if (empty($repos)) 
    {

        break;

    }
    
    foreach ($repos as $repo) 
    {

        $name = addslashes($repo['name']);
        $description = addslashes($repo['description'] ?? '');
        $url = addslashes($repo['html_url']);
        
        // Check if URL already exists
        $checkQuery = 'SELECT id 
            FROM applications 
            WHERE url = "'.$url.'"
            LIMIT 1';
        $result = mysqli_query($connect, $checkQuery);
        
        if (mysqli_num_rows($result) == 0) 
        {

            $query = "INSERT INTO applications (
                    github_name, 
                    github_url, 
                    description, 
                    forks,
                    stars, 
                    timesheets, 
                    toggle, 
                    category_id, 
                    host_id, 
                    created_at, 
                    updated_at
                ) VALUES (
                    '{$name}',
                    '{$url}',
                    '{$description}',
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    NOW(),
                    NOW()
                )";
            mysqli_query($connect, $query);
            echo 'Added: '.$repo['name'].'<br>';

        }
        else
        {
            echo 'Skipped (exists): '.$repo['name'].'<br>';
        }

    }
    
    $page++;

}
