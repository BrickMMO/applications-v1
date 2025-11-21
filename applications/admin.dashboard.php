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

$query = 'SELECT applications.*,
    categories.name AS category_name,
    hosts.image AS host_image
    FROM applications
    LEFT JOIN categories
    ON applications.category_id = categories.id
    LEFT JOIN hosts
    ON applications.host_id = hosts.id
    ORDER BY github_name ASC';    
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
        <td class="w3-center" style="width:60px;"></td>
        <td class="w3-center" style="width:60px;"></td>
        <th>Name</th>
        <th><GitHub</th>
        <th class="bm-table-icon"><i class="fa-regular fa-calendar" title="Timesheets"></i></th>
        <th class="bm-table-icon"><i class="fa-solid fa-toggle-on" title="Toggle"></i></th>
        <th class="bm-table-icon"><i class="fa-solid fa-star" title="Stars"></i></th>
        <th class="bm-table-icon"><i class="fa-solid fa-code-fork" title="Forks"></i></th>
        <th class="bm-table-icon"></th>
        <th class="bm-table-icon"></th>
    </tr>

    <?php while ($record = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td class="w3-center" style="width:60px;">
                <?php if (!empty($record['icon'])): ?>
                    <img src="<?=htmlspecialchars($record['icon'])?>" alt="icon" style="width:50px;height:50px;object-fit:contain;">
                <?php endif; ?>
            </td>
            <td class="w3-center" style="width:60px;">
                <?php if (!empty($record['host_image'])): ?>
                    <img src="<?=htmlspecialchars($record['host_image'])?>" alt="host icon" style="width:50px;height:50px;object-fit:contain;">
                <?php endif; ?>
            </td>
            <td>
                <?=$record['name']?>
                <small>
                    <?php if($record['url']): ?>
                        <br>
                        <a href="<?=string_url_local($record['url'])?>"><?=string_url_local($record['url'])?></a>
                    <?php endif; ?>
                    <?php if($record['category_name']): ?>
                        <br>
                        <?=$record['category_name']?>
                    <?php endif; ?>
                </small>
            </td>
            <td>
                <?=$record['github_name']?>
                <?php if($record['github_url']): ?>
                    <br>
                    <small>
                        <a href="<?=$record['github_url']?>"><?=$record['github_url']?></a>
                    </small>
                <?php endif; ?>
            </td>
            <td class="w3-center">
                <?php if($record['timesheets'] == 1): ?>
                    <i class="fa-solid fa-toggle-on" style="color: #ff5b00;"></i>
                <?php else: ?>
                    <i class="fa-solid fa-toggle-off" style="color: #888;"></i>
                <?php endif; ?>
            </td>
            <td class="w3-center">
                <?php if($record['toggle'] == 1): ?>
                    <i class="fa-solid fa-toggle-on" style="color: #ff5b00;"></i>
                <?php else: ?>
                    <i class="fa-solid fa-toggle-off" style="color: #888;"></i>
                <?php endif; ?>
            </td>
            <td class="w3-center">
                <?=$record['stars']?>
            </td>
            <td class="w3-center">
                <?=$record['forks']?>
            </td>
            <td class="w3-center">
                <a href="/admin/edit/<?=$record['id'] ?>">
                    <i class="fa-solid fa-pencil"></i>
                </a>
            </td>
            <td class="w3-center">
                <a href="#" onclick="return confirmModal('Are you sure you want to delete the application <?=$record['name'] ?>?', '/admin/dashboard/delete/<?=$record['id'] ?>');">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
