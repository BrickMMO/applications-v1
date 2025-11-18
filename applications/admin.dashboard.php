<?php

security_check();
admin_check();

if (isset($_GET['delete'])) 
{

    $query = 'DELETE FROM applications 
        WHERE id = '.$_GET['delete'].'
        LIMIT 1';
    mysqli_query($connect, $query);

    message_set('Delete Success', 'Application has been deleted.');
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
    ORDER BY name ASC';    
$result = mysqli_query($connect, $query);

$applications_count = mysqli_num_rows($result);

?>

<h1 class="w3-margin-top w3-margin-bottom">
    <img
        src="https://cdn.brickmmo.com/icons@1.0.0/bricksum.png"
        height="50"
        style="vertical-align: top"
    />
    Applications
</h1>

<p>
    Number of applications: <span class="w3-tag w3-blue"><?=$applications_count?></span>    
</p>

<hr />

<h2>Application List</h2>

<table class="w3-table w3-bordered w3-striped w3-margin-bottom">
    <tr>
        <th>Name</th>
        <th>Timesheets</th>
        <th>Stars</th>
        <th>Forks</th>
        <th class="bm-table-icon"></th>
        <th class="bm-table-icon"></th>
    </tr>

    <?php while ($record = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td>
                <a href="<?=$record['url']?>" target="_blank"><?=$record['name']?></a>
                <br>
                <small><?=string_shorten($record['description'], 100)?></small>
            </td>
            <td class="w3-center">
                <?php if($record['timesheets'] == 1): ?>
                    <i class="fa-solid fa-toggle-on" style="color: #ff5b00;"></i>
                <?php else: ?>
                    <i class="fa-solid fa-toggle-off" style="color: #888;"></i>
                <?php endif; ?>
            </td>
            <td>
                <?=$record['stars']?>
            </td>
            <td>
                <?=$record['forks']?>
            </td>
            <td>
                <a href="/admin/edit/<?=$record['id'] ?>">
                    <i class="fa-solid fa-pencil"></i>
                </a>
            </td>
            <td>
                <a href="#" onclick="return confirmModal('Are you sure you want to delete the application <?=$record['name'] ?>?', '/admin/dashboard/delete/<?=$record['id'] ?>');">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

<a
    href="/admin/add"
    class="w3-button w3-white w3-border"
>
    <i class="fa-solid fa-pen-to-square fa-padding-right"></i> Add Application
</a>


<!--
<a
    href="/admin/import"
    class="w3-button w3-white w3-border"
>
    <i class="fa-solid fa-download"></i> Import Colours
</a>

<hr />

<div
    class="w3-row-padding"
    style="margin-left: -16px; margin-right: -16px"
>
    <div class="w3-half">
        <div class="w3-card">
            <header class="w3-container w3-grey w3-padding w3-text-white">
                <i class="bm-colours"></i> Uptime Status
            </header>
            <div class="w3-container w3-padding">Uptime Status Summary</div>
            <footer class="w3-container w3-border-top w3-padding">
                <a
                    href="/admin/uptime/colours"
                    class="w3-button w3-border w3-white"
                >
                    <i class="fa-regular fa-file-lines fa-padding-right"></i>
                    Full Report
                </a>
            </footer>
        </div>
    </div>
    <div class="w3-half">
        <div class="w3-card">
            <header class="w3-container w3-grey w3-padding w3-text-white">
                <i class="bm-colours"></i> Stat Summary
            </header>
            <div class="w3-container w3-padding">App Statistics Summary</div>
            <footer class="w3-container w3-border-top w3-padding">
                <a
                    href="/stats/colours"
                    class="w3-button w3-border w3-white"
                >
                    <i class="fa-regular fa-chart-bar fa-padding-right"></i> Full Report
                </a>
            </footer>
        </div>
    </div>
</div>
-->

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
