<script>
const BASEURL = <?= json_encode(base_url()) ?>;
const ENVIRONMENT = <?=json_encode(ENVIRONMENT)?>;
const DEBUGMODE = <?=json_encode($ci->config->item('debug_mode'))?>;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="{{base_url(JSPATH . 'jquery.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url(JSPATH . 'jquery.nicescroll.min.js?v=' . ADMIN_VERSION) }}"></script>
<script src="{{base_url(JSPATH . 'start.min.js?v=' . ADMIN_VERSION)}}"></script>
@isset($footer_includes)
	@foreach($footer_includes as $include)
	<?php echo $include ?>
	@endforeach
@endisset