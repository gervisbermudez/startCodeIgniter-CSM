<script>
const BASEURL = <?php echo json_encode(base_url()) ?>;
const ADMIN_VERSION = <?php echo json_encode(ADMIN_VERSION) ?>;
const SITE_TITLE = <?php echo json_encode(config("SITE_TITLE")) ?>;
const ENVIRONMENT = <?php echo json_encode(ENVIRONMENT) ?>;
const DEBUGMODE = <?php echo json_encode($ci->config->item('debug_mode')) ?>;
window.ADMIN_LANG = {
  toast_error: <?php echo json_encode(lang('search_error')); ?>,
  toast_saved: <?php echo json_encode(lang('toast_saved')); ?>,
  toast_form_invalid: <?php echo json_encode(lang('toast_form_invalid')); ?>,
  toast_deleted: <?php echo json_encode(lang('toast_deleted')); ?>,
  toast_archived: <?php echo json_encode(lang('toast_archived')); ?>,
  toast_done: <?php echo json_encode(lang('toast_done')); ?>,
  toast_duplicated: <?php echo json_encode(lang('toast_duplicated')); ?>,
  toast_restored: <?php echo json_encode(lang('toast_restored')); ?>,
  notifications_title: <?php echo json_encode(lang('notifications_title')); ?>,
  notifications_all: <?php echo json_encode(lang('notifications_all')); ?>,
  notifications_unread: <?php echo json_encode(lang('notifications_unread')); ?>,
  notifications_read: <?php echo json_encode(lang('notifications_read')); ?>,
  notifications_filter_all: <?php echo json_encode(lang('notifications_filter_all')); ?>,
  notifications_empty: <?php echo json_encode(lang('notifications_empty')); ?>,
  notifications_empty_hint: <?php echo json_encode(lang('notifications_empty_hint')); ?>,
  notifications_view_all: <?php echo json_encode(lang('notifications_view_all')); ?>,
  notifications_mark_read: <?php echo json_encode(lang('notifications_mark_read')); ?>,
  notifications_mark_all: <?php echo json_encode(lang('notifications_mark_all')); ?>,
  notifications_bell: <?php echo json_encode(lang('notifications_bell')); ?>,
  notifications_marked: <?php echo json_encode(lang('notifications_marked')); ?>
};
function lang(key) {
  var dict = window.ADMIN_LANG || {};
  return dict[key] ? dict[key] : key;
}
</script>
@if (ENVIRONMENT == 'production'):
<script src="{{base_url('public/vendors/vue/vue.min.js?v=' . ADMIN_VERSION)}}"></script>
@else
<script src="{{base_url('public/vendors/vue/vue.js?v=' . ADMIN_VERSION)}}"></script>
@endif
<script src="{{base_url('public/js/admin-runtime.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/admin-chrome.js?v=' . ADMIN_VERSION)}}"></script>
@isset($footer_includes)
@foreach($footer_includes as $include)
<?php echo $include ?>
@endforeach
@endisset