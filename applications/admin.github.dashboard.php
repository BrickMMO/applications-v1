<?php

security_check();
admin_check();

define('APP_NAME', 'Applications');
define('PAGE_TITLE', 'GitHub Scanner');
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
    ORDER BY error_count DESC
    LIMIT 6';
$result = mysqli_query($connect, $query);

?>

<!-- CONENT -->

<h1 class="w3-margin-top w3-margin-bottom">
    <img
        src="https://cdn.brickmmo.com/icons@1.0.0/colours.png"
        height="50"
        style="vertical-align: top"
    />
    Applications
</h1>
<p>
    <a href="<?=ENV_DOMAIN?>/admin/dashboard">Applications</a> / 
    GitHub Scanner
</p>

<hr>

<h2>GitHub Scanner</h2>

<p>
    Currently scanning: <span class="w3-tag w3-blue"><?=$github_accounts?></span>
</p>
<p>
    Number of repos scanned: <span class="w3-tag w3-blue"><?=$github_repos_scanned?></span> 
</p>
<p>
    Last import: <span class="w3-tag w3-blue"><?=(new DateTime($github_last_import))->format("D, M j g:i A")?></span>
</p>

<hr />

<h2>Repo Fix List</h2>

<?php if(mysqli_num_rows($result) == 0): ?>
    
<?php else: ?>

    <div class="w3-flex" style="flex-wrap: wrap; gap: 16px; align-items: stretch;">

        <?php while($record = mysqli_fetch_assoc($result)): ?>


            <div style="width: calc(33.3% - 16px); box-sizing: border-box; display: flex; flex-direction: column;">
                <div class="w3-card-4 w3-margin-top" style="max-width:100%; height: 100%; display: flex; flex-direction: column;">

                    <header class="w3-container w3-black">
                        <h4 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <i class="fa-brands fa-github"></i>
                            <?=$record['name']?>
                        </h4>
                    </header>

                    <div class="w3-container w3-light-grey w3-margin w3-padding">
                        <span class="w3-bold">
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                            <?=count(explode(chr(13), $record['error_comments']))?>
                            Errors Found
                        </span>
                        <br>
                        <?=explode(chr(13), $record['error_comments'])[0]?> 
                    </div>
                    
                    <div class="w3-container w3-center w3-margin-bottom">
                        <a href="<?=ENV_DOMAIN?>/admin/github/details/<?=$record['name']?>" class="w3-button w3-white w3-border ">
                            <i class="fa-solid fa-circle-info"></i> Details
                        </a>
                        <a href="https://github.com/<?=$record['owner']?>/<?=$record['name']?>" class="w3-button w3-white w3-border ">
                            <i class="fa-brands fa-github"></i> GitHub
                        </a>
                    </div>

                </div>
            </div>
            
        <?php endwhile; ?>

    </div>

<?php endif; ?>

<hr>

<a
    href="<?=ENV_DOMAIN?>/admin/github/results/"
    class="w3-button w3-white w3-border"
>
    <i class="fa-solid fa-magnifying-glass fa-padding-right"></i> View all Results
</a>

<?php foreach(explode(',', $github_accounts) as $account): ?>

    <a
        href="<?=ENV_DOMAIN?>/admin/github/import/<?=$account?>"
        class="w3-button w3-white w3-border"
    >
        <i class="fa-solid fa-pen-to-square fa-padding-right"></i> Import <?=$account?>
    </a>

<?php endforeach; ?>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
