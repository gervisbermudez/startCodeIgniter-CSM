@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fullcalendar/main.min.css')?>">
@endsection

@section('content')
<div id="root" class="calendar-page">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_events',
        'ledeKey' => 'calendar_lede',
    ])

    <div class="status-filters" v-cloak v-show="!loader">
        @if(has_permisions('SELECT_EVENTS'))
        <a class="status-chip" href="{{ base_url('admin/events') }}">{{ lang('events_view_list') }}</a>
        @endif
        <a class="status-chip active" href="{{ base_url('admin/events/calendar') }}" aria-current="page">{{ lang('menu_calendar') }}</a>
    </div>

    <div class="calendar-shell">
        <div class="calendar-shell__loader center" v-show="loader" v-cloak>
            <preloader />
        </div>
        <p class="calendar-hint" v-cloak v-show="!loader && isEmpty">
            <?php echo lang('calendar_empty'); ?>
            @if(has_permisions('CREATE_EVENT'))
            <?php echo ' ' . lang('calendar_empty_hint'); ?>
            @endif
        </p>
        <div id="calendar"></div>
    </div>

    <div class="calendar-popover" v-if="selected" v-cloak @click.self="selected = null">
        <div class="calendar-popover__card" role="dialog" aria-modal="true" :aria-label="selected.title">
            <button type="button" class="calendar-popover__close btn-flat" @click="selected = null" :aria-label="lang('calendar_close')">
                <i class="material-icons">close</i>
            </button>
            <h2 class="page-header">@{{ selected.title }}</h2>
            <p class="calendar-popover__when">
                <i class="material-icons tiny" aria-hidden="true">schedule</i>
                @{{ formatSelectedWhen(selected) }}
            </p>
            <p class="calendar-popover__place" v-if="selected.place">
                <i class="material-icons tiny" aria-hidden="true">place</i>
                @{{ selected.place }}
            </p>
            <p>
                <span class="custom-badge" :class="statusClass(selected.status)">@{{ statusLabel(selected.status) }}</span>
            </p>
            <div class="calendar-popover__actions">
                <a v-if="selected.editUrl && canEditSelected(selected)" class="btn" :href="selected.editUrl">{{ lang('edit') }}</a>
                <a v-if="selected.publicUrl" class="btn-flat" :href="selected.publicUrl" target="_blank" rel="noopener">{{ lang('calendar_view_site') }}</a>
            </div>
        </div>
    </div>
</div>
@if(has_permisions('CREATE_EVENT'))
<div class="fixed-action-btn calendar-page-fab">
    <a class="btn-floating btn-large waves-effect st-accent tooltipped"
       data-position="left"
       data-delay="50"
       data-tooltip="<?php echo htmlspecialchars(lang('tooltip_new_event'), ENT_QUOTES, 'UTF-8'); ?>"
       aria-label="<?php echo htmlspecialchars(lang('tooltip_new_event'), ENT_QUOTES, 'UTF-8'); ?>"
       href="{{ base_url('admin/events/add') }}">
        <i class="material-icons">add</i>
    </a>
</div>
@endif
@endsection

@section('footer_includes')
<script>
window.CALENDAR_PERMS = {
  create: <?= has_permisions('CREATE_EVENT') ? 'true' : 'false' ?>,
  update: <?= has_permisions('UPDATE_EVENT') ? 'true' : 'false' ?>,
  selectEvents: <?= has_permisions('SELECT_EVENTS') ? 'true' : 'false' ?>
};
window.CALENDAR_LOCALE = <?= json_encode($this->config->item('language') === 'spanish' ? 'es' : 'en'); ?>;
window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, {
  calendar_lede: <?= json_encode(lang('calendar_lede')); ?>,
  calendar_empty: <?= json_encode(lang('calendar_empty')); ?>,
  calendar_view_site: <?= json_encode(lang('calendar_view_site')); ?>,
  calendar_list: <?= json_encode(lang('calendar_list')); ?>,
  calendar_close: <?= json_encode(lang('calendar_close')); ?>,
  calendar_when: <?= json_encode(lang('calendar_when')); ?>,
  menu_events: <?= json_encode(lang('menu_events')); ?>,
  edit: <?= json_encode(lang('edit')); ?>,
  published: <?= json_encode(lang('published')); ?>,
  draft: <?= json_encode(lang('draft')); ?>,
  archived: <?= json_encode(lang('archived')); ?>,
  events_all_day: <?= json_encode(lang('events_all_day')); ?>
});
</script>
<script src="{{base_url('public/vendors/fullcalendar/main.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/CalendarList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
