@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div id="root" class="notifications-page">
    @include('admin.components.page_intro', [
        'titleKey' => 'notifications_title',
        'ledeKey' => 'notifications_lede',
    ])
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'notifications-search',
        'refreshMethod' => 'getNotifications()',
        'showViewToggle' => false,
        'navbarShow' => '!loader',
        'section' => 'nav-open',
    ])
    <div class="page-navbar__filters">
        <div class="filter-group" role="group" aria-label="<?= htmlspecialchars(lang('status'), ENT_QUOTES, 'UTF-8') ?>">
            <button type="button" class="status-chip" :class="{ active: statusFilter === '1' }" v-on:click="setFilter('1')">{{ lang('notifications_unread') }}</button>
            <button type="button" class="status-chip" :class="{ active: statusFilter === '2' }" v-on:click="setFilter('2')">{{ lang('notifications_read') }}</button>
            <button type="button" class="status-chip" :class="{ active: statusFilter === 'all' }" v-on:click="setFilter('all')">{{ lang('notifications_filter_all') }}</button>
        </div>
        <button type="button" class="btn-flat" v-on:click="markAllRead" v-show="statusFilter !== '2'">{{ lang('notifications_mark_all') }}</button>
    </div>
    @include('admin.components.page_navbar', [
        'refreshMethod' => 'getNotifications()',
        'showViewToggle' => false,
        'navbarShow' => '!loader',
        'itemsExpr' => 'filterAll',
        'section' => 'nav-close',
    ])
    <div class="container" v-cloak v-show="!loader">
        <div class="pages" v-if="filterAll.length > 0">
            <table>
                <thead>
                    <tr>
                        <th>{{ lang('title') }}</th>
                        <th>{{ lang('description') }}</th>
                        <th>{{ lang('date') }}</th>
                        <th>{{ lang('status') }}</th>
                        <th>{{ lang('options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(notification, index) in filterAll" :key="notification.notification_id">
                        <td>
                            <a href="#!" v-on:click.prevent="openNotification(notification, index)">@{{ notification.title }}</a>
                        </td>
                        <td>@{{ getcontentText(notification.description, 80) }}</td>
                        <td>@{{ notification.date_create }}</td>
                        <td>
                            <span v-if="notification.status == 1">{{ lang('notifications_unread') }}</span>
                            <span v-else>{{ lang('notifications_read') }}</span>
                        </td>
                        <td>
                            <a
                                href="#!"
                                class="tooltipped"
                                data-position="left"
                                data-tooltip="{{ lang('notifications_mark_read') }}"
                                aria-label="{{ lang('notifications_mark_read') }}"
                                v-if="notification.status == 1"
                                v-on:click.prevent="markRead(notification, index, false)"
                            >
                                <i class="material-icons" aria-hidden="true">done</i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="notifications-empty center" v-if="!filter && notifications.length === 0 && statusFilter === 'all'">
            <i class="material-icons" aria-hidden="true">notifications_none</i>
            <p class="page-header">{{ lang('notifications_empty') }}</p>
            <p>{{ lang('notifications_empty_hint') }}</p>
        </div>
        @include('admin.components.list_filter_empty', [
            'showExpr' => '!loader && !filter && notifications.length === 0 && statusFilter !== \'all\'',
            'clearMethod' => 'clearListFilters()',
        ])
    </div>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/NotificationsLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
