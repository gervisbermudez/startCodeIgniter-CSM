{{--
    Chips de status en la toolbar (entre nav-open y nav-close).
    $click: método Vue (getMenus). $mode: cms (0–3) | binary (1 / 2).
    $onLabel / $offLabel para binary.
--}}
<?php
if (!isset($click) || $click === '') {
    $click = 'fetchList';
}
if (!isset($mode) || $mode === '') {
    $mode = 'cms';
}
if (!isset($onLabel) || $onLabel === '') {
    $onLabel = lang('published');
}
if (!isset($offLabel) || $offLabel === '') {
    $offLabel = lang('draft');
}
$onValue = isset($onValue) ? (int) $onValue : 1;
$offValue = isset($offValue) ? (int) $offValue : 2;
$statusLabel = htmlspecialchars(lang('status'), ENT_QUOTES, 'UTF-8');
?>
<div class="page-navbar__filters">
    <div class="filter-group" role="group" aria-label="<?= $statusLabel ?>">
        <button type="button" class="status-chip" :class="{active: currentStatus === null}" @click="<?= $click ?>(null)">
            <?= lang('menu_all') ?>
        </button>
        <?php if ($mode === 'binary'): ?>
        <button type="button" class="status-chip" :class="{active: currentStatus === <?= $onValue ?>}" @click="<?= $click ?>(<?= $onValue ?>)">
            <?= $onLabel ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === <?= $offValue ?>}" @click="<?= $click ?>(<?= $offValue ?>)">
            <?= $offLabel ?>
        </button>
        <?php else: ?>
        <button type="button" class="status-chip" :class="{active: currentStatus === 1}" @click="<?= $click ?>(1)">
            <?= lang('published') ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === 2}" @click="<?= $click ?>(2)">
            <?= lang('draft') ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === 3}" @click="<?= $click ?>(3)">
            <?= lang('archived') ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === 0}" @click="<?= $click ?>(0)">
            <?= lang('deleted') ?>
        </button>
        <?php endif; ?>
    </div>
</div>
