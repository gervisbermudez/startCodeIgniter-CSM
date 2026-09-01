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
        'itemsExpr' => 'filterAll',
    ])
    <div class="notifications-filters" v-cloak v-show="!loader">
        <a href="#!" class="chip" :class="{ active: statusFilter === '1' }" v-on:click.prevent="setFilter('1')">{{ lang('notifications_unread') }}</a>
        <a href="#!" class="chip" :class="{ active: statusFilter === '2' }" v-on:click.prevent="setFilter('2')">{{ lang('notifications_read') }}</a>
        <a href="#!" class="chip" :class="{ active: statusFilter === 'all' }" v-on:click.prevent="setFilter('all')">{{ lang('notifications_filter_all') }}</a>
        <a href="#!" class="btn-flat" v-on:click.prevent="markAllRead" v-show="statusFilter !== '2'">{{ lang('notifications_mark_all') }}</a>
    </div>
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
        <div class="notifications-empty center" v-if="!filter && notifications.length === 0">
            <i class="material-icons" aria-hidden="true">notifications_none</i>
            <p class="page-header">{{ lang('notifications_empty') }}</p>
            <p>{{ lang('notifications_empty_hint') }}</p>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/NotificationsLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
