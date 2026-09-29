<?php include 'header.php'; ?>

<style>
.gallery-section {
    padding: 50px;
    text-align: center;
    color: #fff;
}
.album-container {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    margin-top: 30px;
}
.album-card {
    width: 240px;
    background: #111629;
    border-radius: 12px;
    padding: 10px;
    text-decoration: none;
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.35);
    transition: 0.3s;
    border: 1px solid rgba(255,255,255,0.06);
}
.album-card:hover {
    transform: scale(1.05);
}
.album-card img {
    width: 100%;
    height: 170px;
    object-fit: cover;
    border-radius: 10px;
}
.album-card h3 {
    margin-top: 10px;
    font-weight: bold;
}
</style>

<section class="gallery-section">
    <h2>My Travel Albums</h2>

    <div class="album-container">
        <?php
        $base = __DIR__ . "/uploads/albums/";
        $webBase = "uploads/albums/";

        if (is_dir($base)) {
            $folders = array_filter(glob($base . '*'), 'is_dir');

            foreach ($folders as $folder) {
                $albumName = basename($folder);
                $safeAlbum = htmlspecialchars($albumName);

                $cover = "";
                $pattern = $folder . "/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}";
                $files = glob($pattern, GLOB_BRACE);

                if (!empty($files)) {
                    $cover = $webBase . $safeAlbum . "/" . htmlspecialchars(basename($files[0]));
                } else {
                    $cover = "images/default-cover.jpg";
                }

                echo '
                <a class="album-card" href="album.php?name=' . $safeAlbum . '">
                    <img src="' . $cover . '" alt="' . $safeAlbum . ' cover">
                    <h3>' . ucfirst($safeAlbum) . '</h3>
                </a>';
            }
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>
