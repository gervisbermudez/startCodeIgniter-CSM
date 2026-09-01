{{--
    Título + promesa de una línea al tope de listas/formularios admin.
    Params: $titleKey / $ledeKey (claves lang()) o $title / $lede (texto ya traducido).
    Un H1 por pantalla.
--}}
<?php
$titleOut = '';
if (!empty($titleKey)) {
    $titleOut = lang($titleKey);
} elseif (!empty($title)) {
    $titleOut = $title;
}
$ledeOut = '';
if (!empty($ledeKey)) {
    $ledeOut = lang($ledeKey);
} elseif (!empty($lede)) {
    $ledeOut = $lede;
}
?>
<header class="page-intro">
    <?php if ($titleOut !== ''): ?>
    <h1 class="page-header"><?php echo htmlspecialchars($titleOut, ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php endif; ?>
    <?php if ($ledeOut !== ''): ?>
    <p class="page-intro__lede"><?php echo htmlspecialchars($ledeOut, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
</header>
