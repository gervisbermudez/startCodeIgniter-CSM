{{--
    Título + promesa de una línea al tope de listas/formularios admin.
    Params: $titleKey, $ledeKey (claves lang()). Un H1 por pantalla.
--}}
<?php
if (!isset($titleKey) || $titleKey === '') {
    $titleKey = '';
}
if (!isset($ledeKey) || $ledeKey === '') {
    $ledeKey = '';
}
?>
<header class="page-intro">
    <?php if ($titleKey !== ''): ?>
    <h1 class="page-header"><?php echo htmlspecialchars(lang($titleKey), ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php endif; ?>
    <?php if ($ledeKey !== ''): ?>
    <p class="page-intro__lede"><?php echo htmlspecialchars(lang($ledeKey), ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
</header>
