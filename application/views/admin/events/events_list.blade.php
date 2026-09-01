@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
@include('admin.components.data_table_component')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_events',
        'ledeKey' => 'events_lede',
    ])
  <data-table
    ref="eventsTable"
    :endpoint="endpoint"
    :module="'admin/events'"
    :colums="colums"
    :index_data="index_data"
    :pagination="true"
    :show_empty_input="false"
    :show_fab="canCreate"
    :can_update="canUpdate"
    :can_delete="canDelete"
    v-on:new="newEvent"
    v-on:edit="editEvent"
    v-on:delete="deleteItem"
    v-on:archive="archiveItem"
  >
    <div slot="filters">
      <div class="status-filters">
        <a class="status-chip active" href="{{ base_url('admin/events') }}" aria-current="page">{{ lang('events_view_list') }}</a>
        @if(has_permisions('SELECT_CALENDAR'))
        <a class="status-chip" href="{{ base_url('admin/events/calendar') }}">{{ lang('menu_calendar') }}</a>
        @endif
      </div>
      <div class="filter-group" role="group" aria-label="<?= htmlspecialchars(lang('filter'), ENT_QUOTES, 'UTF-8') ?>">
        <button type="button" class="status-chip" :class="{ active: when === 'all' }" @click="setWhen('all')">{{ lang('events_when_all') }}</button>
        <button type="button" class="status-chip" :class="{ active: when === 'upcoming' }" @click="setWhen('upcoming')">{{ lang('events_upcoming') }}</button>
        <button type="button" class="status-chip" :class="{ active: when === 'past' }" @click="setWhen('past')">{{ lang('events_past') }}</button>
      </div>
    </div>
  </data-table>
  <div class="container center" v-show="tableEmpty" v-cloak>
    <i class="material-icons large grey-text">event</i>
    <p class="page-header">{{ lang('events_empty') }}</p>
    @if(has_permisions('CREATE_EVENT'))
    <a href="{{ base_url('admin/events/add') }}" class="btn">{{ lang('events_empty_cta') }}</a>
    @endif
  </div>
  @include('admin.components.list_filter_empty', [
      'showExpr' => 'filterEmpty',
      'clearMethod' => "setWhen('all')",
  ])
</div>
<script>
window.EVENTS_I18N = {
  name: <?= json_encode(lang('events_name')); ?>,
  start: <?= json_encode(lang('events_start')); ?>,
  address: <?= json_encode(lang('events_address')); ?>,
  status: <?= json_encode(lang('events_status')); ?>,
  options: <?= json_encode(lang('events_options')); ?>,
  online: <?= json_encode(lang('events_online')); ?>
};
window.EVENTS_PERMS = {
  create: <?= has_permisions('CREATE_EVENT') ? 'true' : 'false' ?>,
  update: <?= has_permisions('UPDATE_EVENT') ? 'true' : 'false' ?>,
  delete: <?= has_permisions('DELETE_EVENT') ? 'true' : 'false' ?>
};
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/EventsList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
