<script>
const BASEURL = <?= json_encode(base_url()) ?>;
const ADMIN_VERSION = <?= json_encode(ADMIN_VERSION) ?>;
const ENVIRONMENT = <?=json_encode(ENVIRONMENT)?>;
const DEBUGMODE = <?=json_encode($ci->config->item('debug_mode'))?>;
</script>
@if (ENVIRONMENT == 'production'):
	<script src="{{base_url(JSPATH . 'vue/vue.min.js?v=' . ADMIN_VERSION)}}"></script>
@else
	<script src="{{base_url(JSPATH . 'vue/vue.js?v=' . ADMIN_VERSION)}}"></script>
@endif
<script src="{{base_url(JSPATH . 'materialize.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url(JSPATH . 'jquery.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url(JSPATH . 'jquery.nicescroll.min.js?v=' . ADMIN_VERSION) }}"></script>
<script src="{{base_url(JSPATH . 'start.min.js?v=' . ADMIN_VERSION)}}"></script>
@isset($footer_includes)
	@foreach($footer_includes as $include)
	<?php echo $include ?>
	@endforeach
@endisset