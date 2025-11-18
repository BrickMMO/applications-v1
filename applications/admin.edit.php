<?php

security_check();
admin_check();

if(
    !isset($_GET['key']) || 
    !is_numeric($_GET['key']))
{
    
    message_set('Tag Error', 'There was an error with the provided application.');
    header_redirect('/admin/dashboard');
}
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    
    $query = 'UPDATE applications SET
        timesheets = "'.addslashes($_POST['timesheets']).'",
        updated_at = NOW()
        WHERE id = '.$_GET['key'].'
        LIMIT 1';
    mysqli_query($connect, $query);

    message_set('Application Success', 'Application has been successfully updated.');
    header_redirect('/admin/dashboard');
    
}

define('APP_NAME', 'Applications');
define('PAGE_TITLE', 'Dashboard');
define('PAGE_SELECTED_SECTION', 'admin-dashboard');
define('PAGE_SELECTED_SUB_PAGE', '/admin/dashboard');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_slideout.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');


$query = 'SELECT *
    FROM applications
    WHERE id = "'.$_GET['key'].'"
    LIMIT 1';
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

?>

<h1 class="w3-margin-top w3-margin-bottom">
    <img
        src="https://cdn.brickmmo.com/icons@1.0.0/applications.png"
        height="50"
        style="vertical-align: top"
    />
    Applications
</h1>
<p>
    <a href="/admin/dashboard">Applications</a> / 
    Edit Application
</p>

<hr>

<h2>Edit Application: <?=$record['name']?></h2>

<!-- Edit form -->
<form
    method="post"
    novalidate
    id="main-form"
>

    <?php

    $timesheets = array('0' => 'Disabled', '1' => 'Enabled');
    echo form_select_array('timesheets', $timesheets, array('selected' => $record['timesheets']));

    ?>
    <label for="timesheet" class="w3-text-gray">
        Timesheet <span id="timesheet-error" class="w3-text-red"></span>
    </label>

    <button class="w3-block w3-btn w3-orange w3-text-white w3-margin-top">
        <i class="fa-solid fa-tag fa-padding-right"></i>
        Edit Application
    </button>

</form>

<?php
include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
