{{--
    Barra de búsqueda interna de listas admin.
    Params: $searchInputId, $refreshMethod, $showViewToggle, $navbarShow, $navbarIf, $resetMethod, $placeholder, $itemsExpr, $section (nav|empty|all)
--}}
<?php
if (!isset($searchInputId) || $searchInputId === '') {
    $searchInputId = 'page-search';
}
if (!isset($refreshMethod) || $refreshMethod === '') {
    $refreshMethod = 'refreshList()';
}
if (!isset($showViewToggle)) {
    $showViewToggle = true;
}
if (!isset($navbarShow) || $navbarShow === '') {
    $navbarShow = '!loader';
}
if (!isset($resetMethod) || $resetMethod === '') {
    $resetMethod = 'resetFilter()';
}
if (!isset($placeholder) || $placeholder === '') {
    $placeholder = lang('search_placeholder');
}
if (!isset($section) || $section === '') {
    $section = 'all';
}
$noResultsPrefix = trim(preg_replace('/%s|"/', '', lang('search_no_results')));
$renderNav = ($section === 'nav' || $section === 'all');
$renderEmpty = ($section === 'empty' || $section === 'all');
?>
<?php if ($renderNav): ?>
<nav class="page-navbar" v-cloak<?php if (!empty($navbarIf)): ?> v-if="<?php echo $navbarIf; ?>"<?php endif; ?> v-show="<?php echo $navbarShow; ?>">
    <div class="nav-wrapper">
        <form v-on:submit.prevent="onNavbarSearch">
            <div class="input-field">
                <input
                    id="<?php echo htmlspecialchars($searchInputId, ENT_QUOTES, 'UTF-8'); ?>"
                    class="input-search"
                    type="search"
                    placeholder="<?php echo htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'); ?>"
                    v-model="filter"
                    aria-label="<?php echo htmlspecialchars(lang('search'), ENT_QUOTES, 'UTF-8'); ?>"
                >
                <label class="label-icon" for="<?php echo htmlspecialchars($searchInputId, ENT_QUOTES, 'UTF-8'); ?>"><i class="material-icons">search</i></label>
                <i class="material-icons" v-on:click="<?php echo $resetMethod; ?>">close</i>
            </div>
        </form>
        <ul class="right page-navbar-actions">
            <?php if ($showViewToggle): ?>
            <li>
                <a
                    href="#!"
                    class="tooltipped"
                    data-position="bottom"
                    data-tooltip="<?php echo htmlspecialchars(lang('toggle_view'), ENT_QUOTES, 'UTF-8'); ?>"
                    aria-label="<?php echo htmlspecialchars(lang('toggle_view'), ENT_QUOTES, 'UTF-8'); ?>"
                    v-on:click.prevent="toggleView"
                >
                    <i class="material-icons">@{{ viewToggleIcon }}</i>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a
                    href="#!"
                    class="tooltipped"
                    data-position="bottom"
                    data-tooltip="<?php echo htmlspecialchars(lang('refresh'), ENT_QUOTES, 'UTF-8'); ?>"
                    aria-label="<?php echo htmlspecialchars(lang('refresh'), ENT_QUOTES, 'UTF-8'); ?>"
                    v-on:click.prevent="<?php echo $refreshMethod; ?>"
                >
                    <i class="material-icons">refresh</i>
                </a>
            </li>
        </ul>
    </div>
</nav>
<?php endif; ?>
<?php if ($renderEmpty && !empty($itemsExpr)): ?>
<div class="page-search-empty" v-cloak v-if="!loader && filter && <?php echo $itemsExpr; ?>.length === 0<?php if (!empty($navbarIf)): ?> && <?php echo $navbarIf; ?><?php endif; ?>">
    <p class="page-header"><?php echo htmlspecialchars($noResultsPrefix, ENT_QUOTES, 'UTF-8'); ?> «<strong>@{{ filter }}</strong>»</p>
    <a href="#!" class="btn-flat" v-on:click.prevent="<?php echo $resetMethod; ?>"><?php echo lang('search_empty_cta'); ?></a>
</div>
<?php endif; ?>
