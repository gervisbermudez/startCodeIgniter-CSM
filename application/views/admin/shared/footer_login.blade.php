<script>
const BASEURL = <?php echo json_encode(base_url()) ?>;
const ADMIN_VERSION = <?php echo json_encode(ADMIN_VERSION) ?>;
const SITE_TITLE = <?php echo json_encode(config("SITE_TITLE")) ?>;
const ENVIRONMENT = <?php echo json_encode(ENVIRONMENT) ?>;
const DEBUGMODE = <?php echo json_encode($ci->config->item('debug_mode')) ?>;
window.ADMIN_LANG = {
  toast_error: <?php echo json_encode(lang('search_error')); ?>,
  toast_saved: <?php echo json_encode(lang('toast_saved')); ?>,
  toast_form_invalid: <?php echo json_encode(lang('toast_form_invalid')); ?>
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
<script src="{{base_url(JSPATH . 'materialize.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url(JSPATH . 'jquery.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/start.js?v=' . ADMIN_VERSION)}}"></script>
@isset($footer_includes)
@foreach($footer_includes as $include)
<?php echo $include ?>
@endforeach
@endisset
