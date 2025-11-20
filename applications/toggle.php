<?php

if(isset($_GET['key']))
{

    $q = string_url($_GET['key']);
    if($q != $_GET['key'])
    {
        header_redirect('/q/'.$q);
    }
 
}

// Get page number from URL if set
if(isset($_GET['page']) && is_numeric($_GET['page']))
{
    $current_page = (int)$_GET['page'];
}
else
{
    $current_page = 1;
}

define('APP_NAME', 'Applications');
define('PAGE_TITLE', 'Applications');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT DISTINCT applications.name,
    applications.url,
    applications.id,
    applications.icon
    FROM applications
    WHERE toggle = 1
    AND markdown = 0
    ORDER BY applications.name';
$application_result = mysqli_query($connect, $query);

$query = 'SELECT DISTINCT applications.name,
    applications.url,
    applications.id,
    applications.icon
    FROM applications
    WHERE toggle = 1
    AND markdown = 1
    ORDER BY applications.name';
$documentation_result = mysqli_query($connect, $query);

?>

<div class="w3-center">

    <h1>Applications</h1>

    <div class="w3-flex" style="flex-wrap: wrap; gap: 0px; align-items: stretch;">

        <?php while ($display = mysqli_fetch_assoc($application_result)): ?>

            <div style="width: calc(12.5% - 0px); box-sizing: border-box; display: flex; flex-direction: column;" class="w3-center">
                <a href="<?=string_url_local($display['url'])?>" class="w3-margin">
                    <img src="<?=$display['icon']?>" style="width: 100%;" alt="" />
                    <br>
                    <?=$display['name']?>
                </a>
            </div>

        <?php endwhile; ?>

    </div>

    <hr>

    <h1>Documentation</h1>

    <div class="w3-flex" style="flex-wrap: wrap; gap: 0px; align-items: stretch;">

        <?php while ($display = mysqli_fetch_assoc($documentation_result)): ?>

            <div style="width: calc(12.5% - 0px); box-sizing: border-box; display: flex; flex-direction: column;" class="w3-center">
                <a href="<?=string_url_local($display['url'])?>" class="w3-margin">
                    <img src="<?=$display['icon']?>" style="width: 100%;" alt="" />
                    <br>
                    <?=$display['name']?>
                </a>
            </div>

        <?php endwhile; ?>

    </div>

</div>

<?php

include('../templates/main_footer.php');
include('../templates/html_footer.php');
