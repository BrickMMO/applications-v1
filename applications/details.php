<?php

define('APP_NAME', 'Applications');
define('PAGE_TITLE', 'Application Details');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_slideout.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT *
    FROM applications
    WHERE id = "'.addslashes($_GET['key']).'"
    LIMIT 1';
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

?>


<div class="w3-center">
    <h1><?=$record['name']?></h1>
</div>

<hr>

<div>
    <p><?=nl2br($record['description']) ?: 'No description available'?></p>
    <p>
        Stars: <span class="w3-bold"><?=$record['stars']?></span>
        <br>
        Forks: <span class="w3-bold"><?=$record['forks']?></span>
    </p>
    <p>
        GitHub: 
        <a href="<?=$record['github_url']?>">
            <span class="w3-bold"><?=$record['github_url']?></span>
        </a>
        <br>
        URL: 
        <a href="<?=$record['url']?>">
            <span class="w3-bold"><?=$record['url']?></span>
        </a>
    </p>
</div>

<hr>

<a href="<?=ENV_DOMAIN?>/q" class="w3-button w3-white w3-border">
    <i class="fa-solid fa-caret-left fa-padding-right"></i>
    Back to Application List
</a>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');