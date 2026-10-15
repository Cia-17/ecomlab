<?php
// Shared page footer: copyright, optional page scripts, closing tags.
// A view can set $page_scripts = ['validate.js'] before including this file.
require_once __DIR__ . "/../../core/core.php";
?>
<footer class="site-footer">
    <div class="container">&copy; <?= date('Y') ?> Shoppn</div>
</footer>
<?php foreach (($page_scripts ?? []) as $script): ?>
<script src="<?= e(base_url()) ?>/js/<?= e($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
