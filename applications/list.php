<?php

define('APP_NAME', 'Applications');
define('PAGE_TITLE', 'Applications');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT *
    FROM applications
    WHERE timesheets = 1
    ORDER BY name ASC';
$result = mysqli_query($connect, $query);

?>

<main>
    
    <div class="w3-center">
        <h1>BrickMMO Applications</h1>
    </div>

    <hr>

    <div class="w3-row-padding" style="display: flex; flex-wrap: wrap;">

        <?php while ($record = mysqli_fetch_assoc($result)): ?>

            <div class="w3-third w3-margin-bottom" style="display: flex;">
                <div class="w3-card-4" style="width: 100%; display: flex; flex-direction: column;">
                    
                    <header class="w3-container w3-black">
                        <h4 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?=$record['name']?></h4>
                    </header>

                    <div class="w3-container w3-padding">
                        <div>
                            <a href="/details/<?=$record['id']?>" class="w3-button w3-border w3-block">
                                <i class="fa-solid fa-circle-info fa-padding-right"></i>Project Details
                            </a>
                        </div>
                        <div class="w3-margin-top">
                            <a href="<?=$record['url']?>" class="w3-button w3-border w3-block">
                                <i class="fa-brands fa-github fa-padding-right"></i>View on GitHub
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>

    </div>

</main>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');