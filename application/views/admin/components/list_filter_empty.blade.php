{{--
    Vacío de filtro (chip o texto que dejó 0 filas). Distinto del vacío de módulo.
    Params: $showExpr (Vue), $clearMethod, $message (opcional, default list_filter_empty)
--}}
<?php
if (!isset($showExpr) || $showExpr === '') {
    $showExpr = '!loader';
}
if (!isset($clearMethod) || $clearMethod === '') {
    $clearMethod = 'resetFilter()';
}
if (!isset($message) || $message === '') {
    $message = lang('list_filter_empty');
}
?>
<div class="container center" v-if="<?php echo $showExpr; ?>" v-cloak>
    <i class="material-icons large grey-text" aria-hidden="true">search</i>
    <p class="page-header"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="#!" class="btn-flat" v-on:click.prevent="<?php echo $clearMethod; ?>"><?php echo lang('filter_empty_cta'); ?></a>
</div>
