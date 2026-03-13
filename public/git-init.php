<?php
/**
 * TRAFFICVAI GIT INITIALIZER
 * This script helps you link your server code to your GitHub repository.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<body style='font-family:sans-serif; background:#0f172a; color:#f1f5f9; padding:20px;'>";
echo "<div style='max-width:900px; margin:auto; background:#1e293b; padding:40px; border-radius:30px; border:1px solid #334155; box-shadow:0 20px 50px rgba(0,0,0,0.3);'>";
echo "<h1 style='color:#60a5fa; font-size:2.5em; margin-bottom:10px;'>Git Initialization Wizard</h1>";
echo "<p style='color:#94a3b8; font-size:1.1em; margin-bottom:40px;'>This tool will prepare your server to receive updates directly from GitHub.</p>";

$basePath = realpath(__DIR__ . '/..');

if (isset($_POST['init'])) {
    $repoUrl = $_POST['repo_url'];
    $branch = $_POST['branch'] ?: 'main';
    
    echo "<div style='background:#000; color:#4ade80; padding:20px; border-radius:15px; font-family:monospace; margin-bottom:30px; border:1px solid #22c55e;'>";
    echo "<h3>Execution Log:</h3><pre>";

    function run($cmd) {
        echo "> $cmd\n";
        $out = shell_exec($cmd . " 2>&1");
        echo trim($out) . "\n\n";
        return trim($out);
    }

    chdir($basePath);

    run("git init");
    run("git config --global --add safe.directory " . $basePath);
    run("git remote add origin $repoUrl");
    run("git fetch origin");
    run("git branch -M $branch");
    run("git reset --hard origin/$branch");
    run("git branch --set-upstream-to=origin/$branch $branch");

    echo "</pre></div>";
    echo "<div style='background:#065f46; color:#a7f3d0; padding:20px; border-radius:15px; border:1px solid #059669;'>";
    echo "✅ <b>Success!</b> Your server is now linked to GitHub. You can now use the 'Check for Updates' button in the Admin Panel.";
    echo "</div>";
    echo "<p style='margin-top:20px;'><a href='index.php' style='color:#60a5fa; text-decoration:none;'>&larr; Go to Site</a></p>";
    exit;
}

?>

<form method="POST" style="display:flex; flex-direction:column; gap:20px;">
    <div>
        <label style="display:block; margin-bottom:8px; font-weight:bold; color:#cbd5e1;">GitHub Repository URL</label>
        <input type="text" name="repo_url" placeholder="https://github.com/username/repo.git" required 
               style="width:100%; padding:15px; border-radius:12px; border:1px solid #475569; background:#1e293b; color:white; font-size:1em;">
        <small style="color:#64748b; display:block; mt-2;">Note: If the repo is private, use: https://user:token@github.com/user/repo.git</small>
    </div>

    <div>
        <label style="display:block; margin-bottom:8px; font-weight:bold; color:#cbd5e1;">Branch Name</label>
        <input type="text" name="branch" value="main" 
               style="width:100%; padding:15px; border-radius:12px; border:1px solid #475569; background:#1e293b; color:white; font-size:1em;">
    </div>

    <button type="submit" name="init" 
            style="background:#3b82f6; color:white; border:none; padding:18px; border-radius:15px; cursor:pointer; font-weight:bold; font-size:1.1em; transition:0.2s;"
            onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
        Initialize GitHub Link
    </button>
</form>

<div style="margin-top:40px; padding-top:20px; border-top:1px solid #334155;">
    <h3 style="color:#94a3b8; font-size:1em; margin-bottom:15px;">System Diagnostics:</h3>
    <?php
    echo "<ul style='color:#64748b; font-size:0.9em;'>";
    echo "<li>Git Version: " . (shell_exec('git --version') ?: '<span style="color:#f87171">Not Found</span>') . "</li>";
    echo "<li>PHP User: " . (get_current_user()) . "</li>";
    echo "<li>Base Path: " . $basePath . "</li>";
    echo "</ul>";
    ?>
</div>

</div></body>
