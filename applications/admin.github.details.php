<?php

security_check();
admin_check();

if(isset($_GET['key']))
{

    $query = 'SELECT *
        FROM repos
        WHERE name = "'.addslashes($_GET['key']).'"
        ORDER BY error_count DESC';
    $result = mysqli_query($connect, $query);

    if(!mysqli_num_rows($result))
    {
        message_set('Repo Error', 'There was an error loading this repo.', 'red');
        header_redirect('/admin/github/dashboard');
    }

    $record = mysqli_fetch_assoc($result);

}
elseif(isset($_GET['rescan']))
{

    $query = 'SELECT *
        FROM repos
        WHERE name = "'.addslashes($_GET['rescan']).'"
        ORDER BY error_count DESC';
    $result = mysqli_query($connect, $query);

    if(!mysqli_num_rows($result))
    {
        message_set('Repo Error', 'There was an error loading this repo.', 'red');
        header_redirect('/admin/github/dashboard');
    }

    $record = mysqli_fetch_assoc($result);

    github_scan_repo($record['owner'], $record['name']);

    message_set('Repo Success', 'Repo has been rescanned.');
    header_redirect('/admin/github/details/'.$record['name']);
    
}

define('APP_NAME', 'Applications');
define('PAGE_TITLE', 'Repo Details');
define('PAGE_SELECTED_SECTION', 'admin-tools');
define('PAGE_SELECTED_SUB_PAGE', '/admin/github/dashboard');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');
include('../templates/message.php');

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
    <a href="/admin/dashboard">Applications</a> / 
    <a href="/admin/github/dashboard">GitHub Scanner</a> / 
    Repo Scan Details    
</p>

<hr />

<h2>Repo Scan Details: <?=$record['name']?></h2>

<p>
    Pull requests: <span class="w3-tag w3-blue"><?=$record['pull_requests']?></span> 
</p>
<p>
    Errors found: <span class="w3-tag w3-blue"><?=$record['error_count']?></span> 
</p>
<p>
    Last scan: <span class="w3-tag w3-blue"><?=(new DateTime($record['updated_at']))->format("D, M j g:i A")?></span>
</p>

<?php if(strlen($record['error_comments']) > 0): ?>

    <hr />

    <h3>Repo Errors</h3>

    <ul class="w3-margin-bottom">
        <?php foreach(explode(chr(13), $record['error_comments']) as $error): ?>
            <li><?=$error?></li>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>

<hr />

<a
    href="/admin/github/details/rescan/<?=$record['name']?>"
    class="w3-button w3-white w3-border"
    onclick="loading();"
>
    <i class="fa-solid fa-pen-to-square fa-padding-right"></i> Rescan Repo
</a>

<a
    href="https://github.com/<?=$record['owner']?>/<?=$record['name']?>"
    class="w3-button w3-white w3-border"
    onclick="loading();"
>
    <i class="fa-brands fa-github" aria-hidden="true"></i> GitHub
</a>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
