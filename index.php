<?php
$pageTitle = 'Shop';
require_once __DIR__ . '/header.php';

$conn = getDB();
$albums = $conn->query("SELECT * FROM albums ORDER BY title ASC");

// Collect unique genres for filter
$genreRes = $conn->query("SELECT DISTINCT genre FROM albums ORDER BY genre ASC");
$genres = [];
while ($g = $genreRes->fetch_assoc()) $genres[] = $g['genre'];
?>

<div class="hero">
    <h1>The World's <span>Finest</span><br>Vinyl Selection</h1>
    <p>Discover rare pressings, timeless classics, and hidden gems — all on wax.</p>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search albums or artists…">
        <button id="searchBtn">Search</button>
    </div>
</div>

<div class="filter-row">
    <button class="genre-btn active" data-genre="all">All</button>
    <?php foreach ($genres as $g): ?>
        <button class="genre-btn" data-genre="<?php echo htmlspecialchars($g); ?>">
            <?php echo htmlspecialchars($g); ?>
        </button>
    <?php endforeach; ?>
</div>

<div class="album-grid">
    <?php if ($albums->num_rows > 0): ?>
        <?php while ($a = $albums->fetch_assoc()): ?>
            <div class="album-card"
                 data-genre="<?php echo htmlspecialchars($a['genre']); ?>"
                 data-title="<?php echo strtolower(htmlspecialchars($a['title'])); ?>"
                 data-artist="<?php echo strtolower(htmlspecialchars($a['artist'])); ?>">

                <div class="album-cover" style="background:<?php echo htmlspecialchars($a['cover_color']); ?>20; border-bottom: 1px solid <?php echo htmlspecialchars($a['cover_color']); ?>40;">
                    <div class="vinyl-disc">
                        <div class="vinyl-label" style="background:<?php echo htmlspecialchars($a['cover_color']); ?>;">
                            <?php echo htmlspecialchars($a['artist']); ?>
                        </div>
                        <div class="vinyl-center-hole"></div>
                    </div>
                </div>

                <div class="album-info">
                    <div class="album-title" title="<?php echo htmlspecialchars($a['title']); ?>">
                        <?php echo htmlspecialchars($a['title']); ?>
                    </div>
                    <div class="album-artist"><?php echo htmlspecialchars($a['artist']); ?> · <?php echo (int)$a['year']; ?></div>
                    <div class="album-meta">
                        <span class="album-price">$<?php echo number_format($a['price'], 2); ?></span>
                        <?php if ((int)$a['stock'] > 0): ?>
                            <span class="album-genre"><?php echo htmlspecialchars($a['genre']); ?></span>
                        <?php else: ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-results">No albums found.</div>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once __DIR__ . '/footer.php';
?>
