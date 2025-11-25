<?php

if(!isset($_GET['key']))
{
    $_GET['key'] = '';
}

$where = '';

if (!empty($_GET['key'])) 
{
    $words = preg_split('/\s+/', string_url_to_text($_GET['key']));
    $search_clauses = [];
    foreach ($words as $word) 
    {
        $word = mysqli_real_escape_string($connect, $word);
        $search_clauses[] = '(applications.name LIKE "%'.$word.'%" OR languages.name LIKE "%'.$word.'%" OR contributors.github_login LIKE "%'.$word.'%")';
    }
    $where .= ' AND (' . implode(' OR ', $search_clauses) . ')';
}

$query = 'SELECT applications.*
    FROM applications
    LEFT JOIN languages
    ON applications.id = languages.application_id
    LEFT JOIN contributors
    ON applications.id = contributors.application_id 
    WHERE ' . $where . '
    GROUP BY applications.id
    ORDER BY name';
$result = mysqli_query($connect, $query);

if(mysqli_num_rows($result))
{

    $colours = array();

    while($colour = mysqli_fetch_assoc($result))
    {

        $colours[]= $colour;
        
    }

    $data = array(
        'message' => 'Search applications retrieved successfully.',
        'error' => false, 
        'colours' => $colours,
    );

}
else
{

    $data = array(
        'message' => 'No matching applications found.',
        'error' => false, 
    );

}