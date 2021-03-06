<style type="text/css">
	@keyframes lds-double-ring {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}

	@-webkit-keyframes lds-double-ring {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}

	@keyframes lds-double-ring_reverse {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(-360deg);
			transform: rotate(-360deg);
		}
	}

	@-webkit-keyframes lds-double-ring_reverse {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(-360deg);
			transform: rotate(-360deg);
		}
	}

	.lds-double-ring {
		position: absolute;
		z-index: 99;
        margin-top: 20%;
	}

	.lds-double-ring div {
		position: absolute;
		width: 160px;
		height: 160px;
		top: 20px;
		left: 20px;
		border-radius: 50%;
		border: 8px solid #000;
		border-color: #1d3f72 transparent #1d3f72 transparent;
		-webkit-animation: lds-double-ring 2s linear infinite;
		animation: lds-double-ring 2s linear infinite;
	}

	.lds-double-ring div:nth-child(2) {
		width: 140px;
		height: 140px;
		top: 30px;
		left: 30px;
		border-color: transparent #5699d2 transparent #5699d2;
		-webkit-animation: lds-double-ring_reverse 2s linear infinite;
		animation: lds-double-ring_reverse 2s linear infinite;
	}

	.lds-double-ring {
		width: 200px !important;
		height: 200px !important;
		-webkit-transform: translate(-100px, -100px) scale(1) translate(100px, 100px);
		transform: translate(-100px, -100px) scale(1) translate(100px, 100px);
	}

</style>
<!-- body -->
<div class="right_col" role="main" style="min-height: 100vh;margin-left:0px">
	<div id="loading" class="lds-css ng-scope" hidden style="position: absolute;
    z-index: 9999999;
    left: 50%;
    top: 45%;">
		<div style="width:100%;height:100%" class="col-md-offset-4 lds-double-ring">
			<div></div>
			<div></div>
		</div>
	</div>

	<div class>
		<div class="page-title">
			
			<div class="clearfix"></div>
			<div id='content' class="row">
				<div class="col-md-12 col-sm-12">
					<div class="x_panel">
						<div class="x_title">
							<div class="col-md-6">
								<h2>
									<?=isset($subTitle)?$subTitle:''?>
								</h2>
							</div>
							<div>
								<?php
									$msg = $this->session->flashdata('msg');
									if (!empty($msg))
									{
										$msg_title = $this->session->flashdata('msg')['title'];
										$msg_text = $this->session->flashdata('msg')['text'];
										$msg_type = $this->session->flashdata('msg')['type'];

								        echo("<link href='".base_url()."vendors/pnotify/dist/pnotify.css' rel='stylesheet'>\n");
								        echo("<link href='".base_url()."vendors/pnotify/dist/pnotify.buttons.css' rel='stylesheet'>\n");
								        echo("<link href='".base_url()."vendors/pnotify/dist/pnotify.nonblock.css' rel='stylesheet'>\n");
						            	?>
						            		<script type="text/javascript">
						            			$(function(){
												    new PNotify({
												        title: '<?=$msg_title;?>',
												        text: '<?=$msg_text;?>',
												        type: '<?=$msg_type;?>',
												        styling: 'bootstrap3'
												    });
												  });
											</script>
						            	<?php
									}
								?>
							</div>