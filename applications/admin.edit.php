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
        name = "'.addslashes($_POST['name']).'",
        url = "'.addslashes($_POST['url']).'",
        icon = "'.addslashes($_POST['icon']).'",
        host_id = "'.addslashes($_POST['host_id']).'",
        timesheets = "'.addslashes($_POST['timesheets']).'",
        category_id = "'.addslashes($_POST['category_id']).'",
        toggle = "'.addslashes($_POST['toggle']).'",
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
    <a href="<?=ENV_DOMAIN?>/admin/dashboard">Applications</a> / 
    Edit Application
</p>

<hr>

<h2>Edit Application: <?=$record['name'] ? $record['name'] : $record['github_name'] ?></h2>

<!-- Edit form -->
<form
    method="post"
    novalidate
    id="main-form"
>
    
    <input  
        name="name" 
        class="w3-input w3-border" 
        type="text" 
        id="name" 
        autocomplete="off"
        value="<?=$record['name']?>"
    />
    <label for="name" class="w3-text-gray">
        Name <span id="name-error" class="w3-text-red"></span>
    </label>

    <input  
        name="url" 
        class="w3-input w3-border w3-margin-top" 
        type="text" 
        id="url" 
        autocomplete="off"
        value="<?=$record['url']?>"
    />
    <label for="url" class="w3-text-gray">
        URL <span id="url-error" class="w3-text-red"></span>
    </label>

        <input  
            name="icon" 
            class="w3-input w3-border w3-margin-top" 
            type="text" 
            id="icon" 
            autocomplete="off"
            value="<?=isset($record['icon']) ? $record['icon'] : ''?>"
        />
        <label for="icon" class="w3-text-gray">
            Icon <span id="icon-error" class="w3-text-red"></span>
        </label>

    <?php echo form_select_table('host_id', 'hosts', 'id', 'name', array('empty_value' => '', 'empty_key' => 0, 'selected' => isset($record['host_id']) ? $record['host_id'] : '')); ?>
    <label for="host_id" class="w3-text-gray">
        Host <span id="host-error" class="w3-text-red"></span>
    </label>

    <?php echo form_select_table('category_id', 'categories', 'id', 'name', array('empty_value' => '', 'empty_key' => 0, 'selected' => isset($record['category_id']) ? $record['category_id'] : '')); ?>
    <label for="host_id" class="w3-text-gray">
        Category <span id="category-error" class="w3-text-red"></span>
    </label>

    <?php
    $timesheets = array('0' => 'No', '1' => 'Yes');
    echo form_select_array('timesheets', $timesheets, array('selected' => $record['timesheets']));
    ?>
    <label for="timesheet" class="w3-text-gray">
        Timesheet <span id="timesheet-error" class="w3-text-red"></span>
    </label>
    
    <?php
    $toggle = array('0' => 'No', '1' => 'Yes');
    echo form_select_array('toggle', $toggle, array('selected' => $record['toggle']));
    ?>
    <label for="toggle" class="w3-text-gray">
        Toggle <span id="toggle-error" class="w3-text-red"></span>
    </label>

    <button class="w3-block w3-btn w3-orange w3-text-white w3-margin-top" onclick="return validateMainForm();">
        <i class="fa-solid fa-tag fa-padding-right"></i>
        Edit Application
    </button>
</form>

<script>

    function validateMainForm() {
        let errors = 0;
        if (errors) return false;
    }

</script>

<?php
include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
