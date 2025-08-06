<?php
function cleanName(string $url): string {
    return preg_replace("/[^a-zA-Z0-9_-]/", "_", $url);
}

$pharPath = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["repo_url"])) {
    $repoUrl = trim($_POST["repo_url"]);
    if (!preg_match("/^(https?:\/\/)?(www\.)?github\.com\/[\w\-]+\/[\w\-]+$/", $repoUrl)) {
        $message = "❌ URL tidak valid. Gunakan URL GitHub seperti https://github.com/pmmp/PocketMine-MP";
    } else {
        $repoUrl = preg_replace("/^https?:\/\//", "", $repoUrl); // buang http:// atau https://
        $repoUrl = "https://" . $repoUrl;

        $repoName = cleanName($repoUrl);
        $repoDir = __DIR__ . "/repos/$repoName";
        $buildDir = __DIR__ . "/builds";
        $pharPath = "$buildDir/$repoName.phar";

        // Hapus folder lama jika ada
        if (is_dir($repoDir)) {
            shell_exec("rm -rf " . escapeshellarg($repoDir));
        }

        // Clone repo
        shell_exec("git clone " . escapeshellarg($repoUrl) . " " . escapeshellarg($repoDir));

        if (is_dir($repoDir)) {
            // Jalankan composer install
            shell_exec("cd " . escapeshellarg($repoDir) . " && composer install --no-dev");

            // Build phar
            shell_exec("cd " . escapeshellarg($repoDir) . " && php build.php");

            // Cari file .phar
            $foundPhar = glob("$repoDir/PocketMine-MP.phar");
            if (!empty($foundPhar)) {
                if (!is_dir($buildDir)) {
                    mkdir($buildDir, 0777, true);
                }
                copy($foundPhar[0], $pharPath);
                $message = "✅ Berhasil diinstall! Tekan tombol Download untuk mengunduh file.";
            } else {
                $message = "❌ Build gagal. File .phar tidak ditemukan.";
                $pharPath = "";
            }
        } else {
            $message = "❌ Gagal clone repo.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PocketMine Installer</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 2rem; }
        input[type=text] { width: 100%; padding: 10px; font-size: 16px; }
        button { padding: 10px 20px; font-size: 16px; margin-top: 1rem; }
        .message { margin-top: 1rem; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Install PocketMine dari Custom GitHub</h2>
    <form method="POST">
        <label>Masukkan Link Repo GitHub:</label><br>
        <input type="text" name="repo_url" placeholder="contoh: https://github.com/pmmp/PocketMine-MP" required><br>
        <button type="submit">Install</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($pharPath) && file_exists($pharPath)): ?>
        <a href="<?= "builds/" . basename($pharPath) ?>" download>
            <button>Download File</button>
        </a>
    <?php endif; ?>
</body>
</html>
