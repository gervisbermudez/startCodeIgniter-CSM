@php
    $item = isset($item) ? $item : 'item';
@endphp
<div class="entity-card-badges">
    <span v-if="{{ $item }}.status == 1" class="custom-badge status-published">
        <i class="material-icons tiny">check_circle</i> <?= lang('published') ?>
    </span>
    <span v-else-if="{{ $item }}.status == 2" class="custom-badge status-draft">
        <i class="material-icons tiny">edit</i> <?= lang('draft') ?>
    </span>
    <span v-else-if="{{ $item }}.status == 3" class="custom-badge status-archived">
        <i class="material-icons tiny">archive</i> <?= lang('archived') ?>
    </span>
    <span v-else-if="{{ $item }}.status == 0" class="custom-badge status-deleted">
        <i class="material-icons tiny">delete_outline</i> <?= lang('deleted') ?>
    </span>
    <span v-if="{{ $item }}.visibility == 1" class="custom-badge visibility-public">
        <i class="material-icons tiny">public</i> <?= lang('public') ?>
    </span>
    <span v-else-if="typeof {{ $item }}.visibility !== 'undefined'" class="custom-badge visibility-private">
        <i class="material-icons tiny">lock</i> <?= lang('private') ?>
    </span>
</div>
