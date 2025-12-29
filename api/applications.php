<?php

$query = 'SELECT * 
    FROM applications ';

if(isset($_GET['timesheets']) && $_GET['timesheets'] == 'true')
{

    $query .= 'WHERE timesheets = 1 ';

} 

$query .= 'ORDER BY name';
$result = mysqli_query($connect, $query);

$applications = array();

if(mysqli_num_rows($result))
{

    while($app = mysqli_fetch_assoc($result))
    {

        $applications[] = $app;

    }

    $data = array(
        'message' => 'Applications with timesheets enabled retrieved successfully.',
        'error' => false,
        'applications' => $applications,
    );

}
else
{

    $data = array(
        'message' => 'No applications with timesheets enabled found.',
        'error' => true,
        'applications' => [],
    );
    
}
