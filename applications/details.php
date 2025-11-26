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
    <p><strong>Description:</strong> <?=nl2br($record['description']) ?: 'No description available'?></p>
    <p><strong>Stars:</strong> <?=$record['stars']?></p>
    <p><strong>Forks:</strong> <?=$record['forks']?></p>
    <p><strong>GitHub:</strong> <a href="<?=$record['github_url']?>"><?=$record['github_url']?></a></p>
    <p><strong>URL:</strong> <a href="<?=$record['url']?>"><?=$record['url']?></a></p>
</div>

<hr>

<a href="/q" class="w3-button w3-white w3-border">
    <i class="fa-solid fa-caret-left fa-padding-right"></i>
    Back to Application List
</a>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');