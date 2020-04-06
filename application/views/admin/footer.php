	<script>
        const DEBUGMODE = <?=json_encode($this->config->item('enable_profiler'))?>;
		const BASEURL = <?= json_encode(base_url()) ?>;
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
	<script src="<?php echo base_url(); ?>public/js/jquery.nicescroll.min.js"></script>
	<script src="<?php echo base_url('public/js/star.js?v=1'); ?>"></script>
	<?php 
	    if (isset($footer_includes)) {
	        foreach ($footer_includes as $key => $value) {
	        echo "$value";
	    	}
	    }
	?>
	</body>
</html>