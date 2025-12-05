<?php

security_check();
admin_check();

if (!isset($_GET['key']) || !in_array($_GET['key'], explode(',', setting_fetch('GITHUB_ACCOUNTS'))))
{
    message_set('Import Error', 'There was an error importing repos.', 'red');
    header_redirect('/admin/github/dashboard');
}

$github_access_token = setting_fetch('GITHUB_ACCESS_TOKEN');

if (!$github_access_token)
{
    message_set('GitHub Error', 'Missing GitHub authentication tokens.', 'red');
    header_redirect('/admin/github/dashboard');
}

define('APP_NAME', 'GitHub Scanner');

define('PAGE_TITLE', 'Dashboard');
define('PAGE_SELECTED_SECTION', 'admin-tools');
define('PAGE_SELECTED_SUB_PAGE', '/admin/github/dashboard');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'DELETE 
    FROM repos 
    WHERE owner = "'.addslashes($_GET['key']).'"';
mysqli_query($connect, $query);

setting_update('GITHUB_LAST_IMPORT', date_now());

?>

<!-- CONENT -->

<h1 class="w3-margin-top w3-margin-bottom">
    <img
        src="https://cdn.brickmmo.com/icons@1.0.0/applications.png"
        height="50"
        style="vertical-align: top"
    />
    Applications
</h1>

<p>
    <a href="/applications/dashboard">Applications</a> / 
    <a href="/admin/github/dashboard">GitHub Tools</a> / 
    Import <?=$_GET['key']?>
</p>

<hr>

<h2>Import Repos</h2>

<p>
    Importing:
    <span class="w3-tag w3-blue" id="repo-count">0/0</span>
</p>
<p>
    Importing from: 
    <span class="w3-tag w3-blue"><?=$_GET['key']?></span>
</p>

<div class="w3-light-grey w3-margin-bottom">
    <div class="w3-container w3-green w3-padding w3-center" style="width:0%; min-width: 50px" id="progress">0%</div>
</div>

<div class="w3-container w3-border w3-padding-16 w3-margin-bottom" id="loading" style="max-height: 500px; overflow: scroll; display: none;">
    <h3>
        <i class="fa-solid fa-spinner fa-spin"></i>
        Loading...
    </h3>
</div>

<a href="#" onclick="startScan(); return false;" class="w3-button w3-margin-bottom w3-border">
    <i class="fa-solid fa-cloud-download-alt fa-padding-right"></i>
    Start Import
</a> 

<script>

    async function fetchAccount(account) {
        return fetch('/ajax/github/account/'+account,{
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                }
            })  
            .then((response)=>response.json())
            .then((responseJson)=>{return responseJson});
    }
    
    async function scanRepo(account,repo) {
        return fetch('/ajax/github/scan/account/'+account+'/repo/'+repo,{
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                }
            })  
            .then((response)=>response.json())
            .then((responseJson)=>{return responseJson});
    }

    function startScan()
    {

        let loading = document.getElementById('loading');
        loading.style.display = "block";

        scanRepos();
    }

    async function scanRepos() {

        let loading = document.getElementById('loading');
        let progress = document.getElementById('progress');
        let repoCount = document.getElementById('repo-count');

        const account = '<?=$_GET['key']?>';
        const resultAccount = await fetchAccount(account);
        
        repoCount.innerHTML = '0/'+resultAccount.repos.length;

        for(let i = 0; i < resultAccount.repos.length; i++)
        {

            let percent = Math.round(((i+1) / resultAccount.repos.length) * 100)+'%';

            progress.innerHTML = percent;
            progress.style.width = percent;

            repoCount.innerHTML = (i+1)+'/'+resultAccount.repos.length;

            const resultRepo = await scanRepo(account,resultAccount.repos[i].name);

            console.log(resultRepo);

            if(i == 0) loading.innerHTML = '';

            let div = document.createElement('div');

            let h3 = document.createElement('h3');
            div.append(h3);

            let h3Text = document.createTextNode(resultRepo.repo.name);
            h3.append(h3Text);

            let p = document.createElement('p');
            p.innerHTML = 'Repo has <span class="w3-tag w3-blue" id="repo-count">'+resultRepo.errors.length+
                '</span> errors and <span class="w3-tag w3-blue" id="repo-count">'+resultRepo.pull_requests+
                '</span> pull requests.';
            div.append(p);

            // let pText = document.createTextNode('Repo has <span class="w3-tag w3-blue" id="repo-count">0/0</span> errors and <span class="w3-tag w3-blue" id="repo-count">0/0</span> pull requests.');
            // p.append(pText);

            let ul = document.createElement('ul');
            // ul.classList.add('w3-ul');
            div.append(ul);

            for(let i = 0; i < resultRepo.errors.length; i++)
            {
                ul.innerHTML += '<li>'+resultRepo.errors[i]+'</li>';
            }

            let links = `<a href="/admin/github/details/${resultRepo.repo.name}" class="w3-button w3-white w3-border ">
                    <i class="fa-solid fa-circle-info"></i> Details
                </a>
                <a href="https:///github.com/${resultRepo.repo.owner}/${resultRepo.repo.name}" class="w3-button w3-white w3-border">
                    <i class="fa-brands fa-github"></i> GitHub
                </a>`;
            div.innerHTML += links;

            // let aText = document.createTextNode(resultRepo.repo.html_url);
            // a.append(aText);

            let hr = document.createElement('hr');
            div.append(hr);

            loading.prepend(div);
             

            await new Promise(resolve => setTimeout(resolve, 0));

        }
            
    }

</script>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
