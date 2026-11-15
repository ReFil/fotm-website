<?php
session_start();

$dbHost = getenv('DB_HOST') ?: 'postgres';
$dbPort = getenv('DB_PORT') ?: '5432';
$dbName = getenv('DB_NAME') ?: 'fotm';
$dbUser = getenv('DB_USER') ?: 'fotm';
$dbPass = getenv('DB_PASSWORD') ?: '';

$atEvent = ((getenv('AT_EVENT') ?: '0') == '1') ? TRUE : FALSE;
$showDect = (((getenv('SHOW_DECT') ?: '0') == '1') && $atEvent) ? TRUE : FALSE;
$location = getenv('LOCATION') ?: 'somewhere';
$phoneNumber = getenv('NUMBER') ?: 'whoops';

$dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName";
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

$schema = 'fotm';
$message = '';
$authError = '';
if($atEvent) {
    $order = "present DESC, random()";
}
else {
    $order = "random()";
}

$stmt = $pdo->query("SELECT id, moonname, pronouns, dect, decription, badge1, badge2, badge3, present FROM $schema.moonmembers ORDER BY $order");
$moonmembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE HTML>
<html lang="en">

<head>
    <link rel="stylesheet" href="./style.css" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link rel="icon" href="assets/moon.ico">
    <title>Friends of the Moon</title>
</head>

<body>
    <script src="./assets/oneko.js"></script>
    <header class="titlebar">
        <h1>Friends of the Moon</h1>

        <div class="links">
            <a href="https://files.friendsofthemoon.space" class="link-item">Files</a>
            <a href="https://place.friendsofthemoon.space" class="link-item">Place</a>
            <a href="https://hunt.friendsofthemoon.space" class="link-item">MoonHunt</a>
        </div>
    </header>

    <main class="content">
        <h2>what are we doing here</h2>
        <p>having fun :)</p>

        <h2>who are we</h2>
        <p>bunch of people who are vaguely tied to southampton from like 2020 onwards and all the people we picked up along the way.</p>

        <h2>how do I get in touch with you?</h2>
        <p><?php 
            if($atEvent) { ?>
                find our assembly at <?= $location ?>, or dial into to fonesofthemoon at <?= $phoneNumber ?>
            <?php }
            else { ?>
                you can probably find our individual members flitting about the interwebs, or come along to our next event and say hello
            <?php } ?>
        </p>

        <div class="people">

            <?php foreach ($moonmembers as $row): ?>
            <article class="person h-card">
                <div class="persontitle">
                    <h3 class="p-name"><?= htmlspecialchars($row["moonname"])?></h3>
                    <div class="holyshitapronounsbox">
                        <?php if ($row["pronouns"] != ''): ?><h4 class="p-gender-identity"><?= htmlspecialchars($row["pronouns"]) ?></h4><?php endif; ?>
                        <?php if (($row["dect"] != '') && $showDect && ($row["present"] == '1')): ?><h4>DECT: <span class="p-tel"><?= htmlspecialchars($row["dect"]) ?></span></h4><?php endif; ?>
                    </div>
                </div>

                <p class="p-role"><?php if ($row["decription"] != ''): ?><?= $row["decription"]?><?php endif; ?></p>

                <div class="badge-grid">
                    <?php if ($row["badge1"] != ''): ?><?= $row["badge1"] ?><?php endif; ?>
                    <?php if ($row["badge2"] != ''): ?><?= $row["badge2"] ?><?php endif; ?>
                    <?php if ($row["badge3"] != ''): ?><?= $row["badge3"] ?><?php endif; ?>


                    </div>
            </article>
            <?php endforeach; ?>

        </div>

        <h2>what can i access on here?</h2>
        <?php 
            if ($atEvent): ?>
            <p>Our minecraft server is available at friendsofthemoon.space:25565. It is a vanilla 26.2 server.</p>
            <p>Our factorio server is available at friendsofthemoon.space:34197 (RCON port 27015). Please note you need the DLC</p>
        <?php endif; ?>

        <p>our fileserver is available at <a href="https://files.friendsofthemoon.space">files.friendofthemoon.space</a>. feel free to upload your own curiosities to the upload folder, and we shall review and move them periodically. please do not upload illegal or explicit material ty</p>
        <p><?php 
            if ($atEvent): ?>
                We run our own mini-poc on site. Feel free to dial into the system at <?= $phoneNumber ?> if you want to get in touch, more fun things may be forthcoming, watch this space
               <?php endif; ?></p>

        <h2>Guestbook</h2>
        <div border="none">
            <iframe align="center" width="100%" height="900px" src="ultraminiguestbook.php" frameborder="0" scrolling="yes" name="myIframe" id="myIframe" allowtransparency="true"> </iframe>

        </div>

    </main>

</body>

</html>
