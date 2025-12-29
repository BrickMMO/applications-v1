<?php

if(isset($_GET['key']))
{

    $q = string_url($_GET['key']);
    if($q != $_GET['key']) header_redirect('/q/'.$q);
 
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

// Pagination setup
$results_per_page = 10;
$offset = ($current_page - 1) * $results_per_page;

// Where clause initialization
$where_clause = 'WHERE timesheets = 1 ';

if(isset($q))
{

    // Split search term by dashes
    $search_terms = explode('-', $q);
    
    // Build WHERE clause for multiple terms
    $where_conditions = [];
    foreach($search_terms as $term) 
    {

        $term = trim($term);

        if(!empty($term)) 
        {

            $term = mysqli_real_escape_string($connect, $term);
            $where_conditions[] = 'applications.name LIKE "%'.$term.'%"';
            $where_conditions[] = 'languages.name LIKE "%'.$term.'%"';
            $where_conditions[] = 'contributors.github_login LIKE "%'.$term.'%"';

        }

    }
    
    $where_clause .= 'AND ('.implode(' OR ', $where_conditions).')';

}

// Count total results
$count_query = 'SELECT COUNT(DISTINCT applications.id) AS total
    FROM applications 
    LEFT JOIN languages
    ON applications.id = languages.application_id
    LEFT JOIN contributors
    ON applications.id = contributors.application_id 
    '.$where_clause;
$count_result = mysqli_query($connect, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_results = $count_row['total'];
$total_pages = ceil($total_results / $results_per_page);

// Get paginated results
$query = 'SELECT DISTINCT applications.name,
    applications.url,
    applications.github_name,
    applications.github_url,
    SUM(contributors.contributions) AS contributions,
    applications.id,
    applications.icon,
    (
        SELECT GROUP_CONCAT(DISTINCT languages.name SEPARATOR ", ")
        FROM languages
        WHERE applications.id = languages.application_id
    ) AS languages
    FROM applications
    LEFT JOIN languages
    ON applications.id = languages.application_id
    LEFT JOIN contributors
    ON applications.id = contributors.application_id 
    '.$where_clause.'
    GROUP BY applications.id
    ORDER BY applications.github_name
    LIMIT '.$offset.', '.$results_per_page;
$result = mysqli_query($connect, $query);

?>
    
<div class="w3-center">

    <h1>Applications</h1>

    <input 
        class="w3-input w3-border w3-margin-top w3-margin-bottom" 
        type="text" 
        value="<?=isset($_GET['key']) ? htmlspecialchars(str_replace('-', ' ', $_GET['key'])) : ''?>"
        placeholder="" 
        style="max-width: 300px; display: inline-block; box-sizing: border-box; vertical-align: middle;" 
        id="search-term">

    <a
        href="#"
        class="w3-button w3-white w3-border w3-margin-top w3-margin-bottom" 
        style="display: inline-block; box-sizing: border-box; vertical-align: middle;"
        id="search-button"
    >
        <i class="fa-solid fa-magnifying-glass"></i> Search
    </a>
    
</div>

<hr>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php
        $start_result = ($current_page - 1) * $results_per_page + 1;
        $end_result = min($current_page * $results_per_page, $total_results);
    ?>

    <p class="w3-center">Displaying <?=$start_result?>-<?=$end_result?> of <?=$total_results?> results</p>

    <table class="w3-table w3-bordered w3-striped w3-margin-bottom">
        <?php while ($display = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td class="w3-center" style="width:60px;">
                    <?php if (!empty($display['icon'])): ?>
                        <img src="<?=htmlspecialchars($display['icon'])?>" alt="icon" style="width:50px;height:50px;object-fit:contain;">
                    <?php else: ?>
                        <span class="w3-text-grey"><i class="fa-regular fa-image fa-2x"></i></span>
                    <?php endif; ?>
                </td>
                <td class="w3-padding">
                    <?php if($display['name']): ?>
                        <?=$display['name']?>
                    <?php else: ?>
                        <?=$display['github_name']?>
                    <?php endif; ?>
                    <br>
                    <small>
                        <?php if($display['url']): ?>
                            <a href="<?=string_url_local($display['url'])?>"><?=string_url_local($display['url'])?></a>
                        <?php else: ?>
                            <a href="<?=$display['github_url']?>"><?=$display['github_url']?></a>
                        <?php endif; ?>
                        <?php if($display['languages']): ?>
                            <br>
                            Languages: <?=$display['languages']?>
                        <?php endif; ?>
                    </small>
                </td>
                <td class="w3-right-align" style="white-space: nowrap;">
                    <a href="<?=ENV_DOMAIN?>/details/<?=$display['id']?>" class="w3-button w3-white w3-border ">
                        <i class="fa-solid fa-circle-info"></i> Details
                    </a>
                    <a href="<?=$display['github_url']?>" class="w3-button w3-white w3-border" target="_blank">
                        <i class="fa-brands fa-github"></i> GitHub
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

<?php else: ?>

    <div class="w3-panel w3-light-grey">
        <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Results Found</h3>
        <p>No results found for <span class="w3-bold"><?=htmlspecialchars(str_replace('-', ' ', $q))?></span>.</p>
    </div>

<?php endif; ?>

<nav class="w3-text-center w3-section">

    <div class="w3-bar">            

        <?php
        
        // Display pagination links
        for ($i = 1; $i <= $total_pages; $i++) 
        {
            echo '<a href="'.ENV_DOMAIN.'/q';
            if($i > 1) echo '/page/'.$i;
            if(isset($q))echo '/'.$q;
            echo '" class="w3-button';
            if($i == $current_page) echo ' w3-border';
            echo '">'.$i.'</a>';
        }

        ?>

    </div>

</nav>

<script>

(function() {
        
    let searchButton = document.getElementById('search-button');
    let searchTerm = document.getElementById('search-term');

    function performSearch() 
    {

        let query = searchTerm.value.trim();

        // Remove anything that's not letters, numbers, or spaces
        query = query.replace(/[^a-zA-Z0-9\s]/g, '');
        // Replace spaces with hyphens
        query = query.replace(/\s+/g, '-');
        window.location.href = '/q/' + query;

    }

    searchButton.addEventListener('click', function(event) 
    {

        event.preventDefault();
        performSearch();

    });

    searchTerm.addEventListener('keypress', function(event) 
    {

        if (event.key === 'Enter') 
        {
            event.preventDefault();
            performSearch();
        }

    });

})();

</script>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
