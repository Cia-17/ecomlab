<?php
// Shows (then clears) the success / error message stored in the session.
foreach (['success' => 'alert-success', 'error' => 'alert-error'] as $key => $class):
    if (!empty($_SESSION[$key])): ?>
<div class="alert <?= $class ?>" role="<?= $key === 'error' ? 'alert' : 'status' ?>"><?= e($_SESSION[$key]) ?></div>
<?php
        unset($_SESSION[$key]);
    endif;
endforeach;
