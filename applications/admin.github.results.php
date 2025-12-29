<?php

security_check();
admin_check();

define('APP_NAME', 'Applications');
define('PAGE_TITLE', 'Scan Results');
define('PAGE_SELECTED_SECTION', 'admin-tools');
define('PAGE_SELECTED_SUB_PAGE', '/admin/github/dashboard');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');
include('../templates/message.php');

$github_accounts = setting_fetch('GITHUB_ACCOUNTS');
$github_last_import = setting_fetch('GITHUB_LAST_IMPORT');
$github_repos_scanned = setting_fetch('GITHUB_REPOS_SCANNED');

$query = 'SELECT *
    FROM repos
    ORDER BY error_count DESC';
$result = mysqli_query($connect, $query);

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
    <a href="<?=ENV_DOMAIN?>/admin/dashboard">Dashboard</a> / 
    <a href="<?=ENV_DOMAIN?>/admin/github/dashboard">GitHub Scanner</a> / 
    Scan Results
</p>

<hr />

<h2>Scan Results</h2>

<table class="w3-table w3-bordered w3-striped w3-margin-bottom">
    <tr>
        <th>Name</th>
        <th class="bm-table-number">Errors</th>
        <th class="bm-table-number">Pulls</th>
        <th></th>
    </tr>

    <?php while($record = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td>
                <?=$record['owner']?>/<?=$record['name']?>
                <br>
                <small>
                    Last Scanned:
                    <span class="w3-bold">
                        <?=time_elapsed_string($record['updated_at'])?>
                    </span>
                </small>
            </td>
            <td class="bm-table-number">
                <?=$record['error_count']?>
            </td>
            <td class="bm-table-number">
                <?=$record['pull_requests']?>
            </td>
            <td class="w3-right-align" style="white-space: nowrap;">
                <a href="<?=ENV_DOMAIN?>/admin/github/details/<?=$record['name']?>" class="w3-button w3-white w3-border ">
                    <i class="fa-solid fa-circle-info"></i> Details
                </a>
                <a href="https://github.com/<?=$record['owner']?>/<?=$record['name']?>" class="w3-button w3-white w3-border">
                    <i class="fa-brands fa-github"></i> GitHub
                </a>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
