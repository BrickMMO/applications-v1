<?php

include('../../includes/connect.php');
include('../../includes/config.php');
include('../../functions/functions.php');

echo '<h2>Updating Application Details</h2>';

// Get 20 oldest updated applications
$query = "SELECT id, github_name, github_url  
    FROM applications 
    -- WHERE id = 93
    ORDER BY updated_at ASC 
    LIMIT 1";
$result = mysqli_query($connect, $query);

$updated = 0;

while ($app = mysqli_fetch_assoc($result)) 
{

    // debug_pre($app);

    echo 'Processing: '.$app['github_name'].'<br>';

    // Extract owner and repo from URL
    // URL format: https://github.com/BrickMMO/repo-name
    $urlParts = explode('/', $app['github_url']);
    $owner = $urlParts[3];
    $repo = $urlParts[4];
    
    // Fetch repo details
    $apiUrl = 'https://api.github.com/repos/'.$owner.'/'.$repo.
        '?client_id='.GITHUB_CLIENT_ID.'&client_secret='.GITHUB_CLIENT_SECRET;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
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

        echo 'Failed to fetch details for: '.$app['github_name'].' (HTTP '.$httpCode.')<br>';
        if ($httpCode == 403) 
        {
            echo 'Rate limit likely reached. Stopping execution.<br>';
            break;
        }
        continue;

    }
    
    $repoData = json_decode($response, true);

    debug_pre($repoData);
    
    $stars = (int)$repoData['stargazers_count'];
    $forks = (int)$repoData['forks_count'];
    
    // Fetch contributors
    $contributorsUrl = $repoData['contributors_url']."?client_id=".GITHUB_CLIENT_ID."&client_secret=".GITHUB_CLIENT_SECRET;
    $chContributors = curl_init();
    curl_setopt($chContributors, CURLOPT_URL, $contributorsUrl);
    curl_setopt($chContributors, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chContributors, CURLOPT_HTTPHEADER, [
        'User-Agent: BrickMMO-Applications',
        'Accept: application/vnd.github+json'
    ]);
    $contributorsResponse = curl_exec($chContributors);
    curl_close($chContributors);


    $query = 'DELETE FROM contributors 
        WHERE application_id = "'.$app['id'].'"';
    mysqli_query($connect, $query);

    if ($contributorsResponse) 
    {

        $contributorsData = json_decode($contributorsResponse, true);

        foreach ($contributorsData as $contributor)
        {

            $query = 'INSERT INTO contributors (
                    application_id,
                    github_login,
                    github_id,
                    contributions,
                    created_at,
                    updated_at
                ) VALUES (
                    '.$app['id'].',
                    "'.addslashes($contributor['login']).'",
                    "'.addslashes($contributor['id']).'",
                    '.$contributor['contributions'].',
                    NOW(),
                    NOW()
                )';
            mysqli_query($connect, $query); 
        }

    }

    // Fetch languages
    $languagesUrl = $repoData['languages_url']."?client_id=".GITHUB_CLIENT_ID."&client_secret=".GITHUB_CLIENT_SECRET;
    $chLang = curl_init();
    curl_setopt($chLang, CURLOPT_URL, $languagesUrl);
    curl_setopt($chLang, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chLang, CURLOPT_HTTPHEADER, [
        'User-Agent: BrickMMO-Applications',
        'Accept: application/vnd.github+json'
    ]);
    $langResponse = curl_exec($chLang);
    curl_close($chLang);

    $query = 'DELETE FROM languages 
        WHERE application_id = "'.$app['id'].'"';
    mysqli_query($connect, $query);

    if ($langResponse) 
    {

        $langData = json_decode($langResponse, true);
    
        foreach ($langData as $language => $lines)
        {

            if( $language !== 'message' &&
                $language !== 'documentation_url' &&
                preg_match('/^[A-Za-z0-9+# ]+$/', $language)) 
            {

                $query = 'INSERT INTO languages (
                        application_id,
                        name,
                        `lines`,
                        created_at,
                        updated_at
                    ) VALUES (
                        '.$app['id'].',
                        "'.addslashes($language).'",
                        '.$lines.',
                        NOW(),
                        NOW()
                    )';
                mysqli_query($connect, $query); 
            }
        }
    }
    
    // Update application
    $updateQuery = 'UPDATE applications SET
        forks = '.$forks.',
        stars = '.$stars.',
        updated_at = NOW()
        WHERE id = '.$app['id'].'
        LIMIT 1';
    
    if (mysqli_query($connect, $updateQuery)) 
    {
        echo 'Updated: '.$app['github_name'].' - Language: '.$language.', Stars: '.$stars.', Forks: '.$forks.'<br>';
        $updated++;
    } 
    else 
    {
        echo 'Failed to update database for: '.$app['github_name'].'<br>';
    }

    /*
    $query = 'SELECT * 
        FROM applications 
        WHERE id = "'.$app['id'].'"
        LIMIT 1';
    $result = mysqli_query($connect, $query);
    $record = mysqli_fetch_assoc($result);

    debug_pre($record);
    */

    // Delay to avoid rate limiting
    sleep(1); // 1 second between each repo

}

echo '<hr><strong>Updated '.$updated.' applications.</strong>';

mysqli_close($connect);
