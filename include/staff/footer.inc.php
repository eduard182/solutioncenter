</div>
</div>
</div>
<?php if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
    <div id="footer">
		<?php include ROOT_DIR . 'awesome/inc/staff-foot.html'; ?>
    </div>
<?php
if(is_object($thisstaff) && $thisstaff->isStaff()) { ?>
    <div class="autocron">
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
        <img src="autocron.php" alt="" width="1" height="1" border="0" />
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
    </div>
<?php
} ?>
</div>
<div id="overlay"></div>
<div id="loading">
    <i class="loading-icon spin"></i>
</div>
<div class="dialog draggable" style="display:none;" id="popup">
    <div id="popup-loading">
        <h1 style="margin-bottom: 20px;"><i class="loading-icon-small spin"></i>
        <?php echo __('Loading ...');?></h1>
    </div>
    <div class="body"></div>
</div>
<div style="display:none;" class="dialog" id="alert">
	<h3 style="padding:0 0 15px 0;">
		<svg style="width:24px;height:24px;position: relative;top: -3px;margin: 0 4px 0 0;" viewBox="0 0 24 24">
			<path style="fill:#d62705;" d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z" />
		</svg>	
		<span id="title"></span>
	</h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <div id="body" style="min-height: 20px;"></div>
    <hr style="margin-top:3em"/>
    <p class="full-width">
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('OK');?>" class="close ok" style="width: 100px!important;padding: 0px!important;margin: 10px 0 0 0!important;">
        </span>
     </p>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery.pjax.js?231f11e"></script>
<script type="text/javascript" src="./js/bootstrap-typeahead.js?231f11e"></script>
<script type="text/javascript" src="./js/scp.js?231f11e"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.10.3.custom.min.js?231f11e"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js?231f11e"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js?231f11e"></script>
<script type="text/javascript" src="./js/tips.js?231f11e"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js?231f11e"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js?231f11e"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-plugins.js?231f11e"></script>
<script type="text/javascript" src="./js/jquery.translatable.js?231f11e"></script>
<script type="text/javascript" src="./js/jquery.dropdown.js?231f11e"></script>
<script type="text/javascript" src="./js/bootstrap-tooltip.js?231f11e"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/fabric.min.js?231f11e"></script>
<link type="text/css" rel="stylesheet" href="./css/tooltip.css?231f11e"/>
<script src="sweetalert2.all.min.js"></script>
<!-- Optional: include a polyfill for ES6 Promises for IE11 -->
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script>
<script type="text/javascript">
    getConfig().resolve(<?php
        include INCLUDE_DIR . 'ajax.config.php';
        $api = new ConfigAjaxAPI();
        print $api->scp(false);
    ?>);
</script>
<?php
if ($thisstaff
        && ($lang = $thisstaff->getLanguage())
        && 0 !== strcasecmp($lang, 'en_US')) { ?>
    <script type="text/javascript" src="ajax.php/i18n/<?php
        echo $thisstaff->getLanguage(); ?>/js"></script>
<?php } ?>
	<script type="text/javascript">
	$(document).ready(function(){
	$('#login').jshake();
	});
	</script>
</body>
</html>
<?php } # endif X_PJAX ?>


<!-- agregado por hdandrea 06-03-18 Implementación jquery Data Table en el dashboard-->
 <script src="<?php echo ROOT_PATH ?>js/jquery.dataTables.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/dataTables.buttons.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/jszip.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/pdfmake.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/vfs_fonts.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/buttons.html5.min.js"></script> 
 
<script type="text/javascript">
$( document ).ready(function() {
    
    
	$('#dashboard_datatable_1').DataTable({
		"dom": 'Bfrtip',
		"buttons": [
			'copyHtml5',
			'excelHtml5',
            {
                text: 'Pdf',
                action: function ( e, dt, button, config ) {
                   
                    
                    var fstart = $("input[name=start]").val();
                    var fselected = $('select[name="period"] option:selected').val();
                     
                    var arr = [];
                    
                    arr.push({"fstart":fstart,"fselected":fselected});
                    arr.push(dt.buttons.exportData());
                     
                    
                     var datajson = JSON.stringify(arr);
                    
                    
					var req = new XMLHttpRequest();
					req.open("POST", "<?=ROOT_PATH; ?>scp/oc_fpdf.php", true);
					req.responseType = "blob";
					req.onreadystatechange = function () {
						if (req.readyState === 4 && req.status === 200) {
							
							var filename = "ORDEN_<?= date("Y-m-d")?>.pdf";
							if (typeof window.chrome !== 'undefined') {
								// Chrome version
								var link = document.createElement('a');
								link.href = window.URL.createObjectURL(req.response);
								link.download = "ORDEN_<?= date("Y-m-d")?>.pdf";
								link.click();
							} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
								// IE version
								var blob = new Blob([req.response], { type: 'application/pdf' });
								window.navigator.msSaveBlob(blob, filename);
							} else {
								// Firefox version
								var file = new File([req.response], filename, { type: 'application/force-download' });
								window.open(URL.createObjectURL(file));
							}
						}
					};
					req.send(datajson);
                    
                }
            }
		],
		"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
		"order": [[ 0, "desc" ]],
		"pagingType": "full_numbers"
	});
	
	
	$('#dashboard_datatable_2').DataTable({
		"dom": 'Bfrtip',
		"buttons": [
			'copyHtml5',
			'excelHtml5',
            {
                text: 'Pdf',
                action: function ( e, dt, button, config ) {
                   
                    
                    var fstart = $("input[name=start]").val();
                    var fselected = $('select[name="period"] option:selected').val();
                     
                    var arr = [];
                    
                    arr.push({"fstart":fstart,"fselected":fselected});
                    arr.push(dt.buttons.exportData());
                     
                    
                     var datajson = JSON.stringify(arr);
                    
                    
					var req = new XMLHttpRequest();
					req.open("POST", "<?=ROOT_PATH; ?>scp/oc_fpdf.php", true);
					req.responseType = "blob";
					req.onreadystatechange = function () {
						if (req.readyState === 4 && req.status === 200) {
							
							var filename = "ORDEN_<?= date("Y-m-d")?>.pdf";
							if (typeof window.chrome !== 'undefined') {
								// Chrome version
								var link = document.createElement('a');
								link.href = window.URL.createObjectURL(req.response);
								link.download = "ORDEN_<?= date("Y-m-d")?>.pdf";
								link.click();
							} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
								// IE version
								var blob = new Blob([req.response], { type: 'application/pdf' });
								window.navigator.msSaveBlob(blob, filename);
							} else {
								// Firefox version
								var file = new File([req.response], filename, { type: 'application/force-download' });
								window.open(URL.createObjectURL(file));
							}
						}
					};
					req.send(datajson);
                    
                }
            }
		],
		"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
		"order": [[ 0, "desc" ]],
		"pagingType": "full_numbers"
	});
	
	
	
	
	
	$('#table_org_orders_1').DataTable({
		"dom": 'Bfrtip',
		"buttons": [
			'copyHtml5',
			'excelHtml5',
            {
                text: 'Pdf',
                action: function ( e, dt, button, config ) {
                   
                    
                    //var fstart = $("input[name=start]").val();
                   // var fselected = $('select[name="period"] option:selected').val();
                     
                    var arr = [];
                    
                  //  arr.push({"fstart":fstart,"fselected":fselected});
                    arr.push(dt.buttons.exportData());
                     
                    
                     var datajson = JSON.stringify(arr);
                    
                    
					var req = new XMLHttpRequest();
					req.open("POST", "<?=ROOT_PATH; ?>scp/oc_org_fpdf.php", true);
					req.responseType = "blob";
					req.onreadystatechange = function () {
						if (req.readyState === 4 && req.status === 200) {
							
							var filename = "ORDEN_<?= date("Y-m-d")?>.pdf";
							if (typeof window.chrome !== 'undefined') {
								// Chrome version
								var link = document.createElement('a');
								link.href = window.URL.createObjectURL(req.response);
								link.download = "ORDEN_<?= date("Y-m-d")?>.pdf";
								link.click();
							} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
								// IE version
								var blob = new Blob([req.response], { type: 'application/pdf' });
								window.navigator.msSaveBlob(blob, filename);
							} else {
								// Firefox version
								var file = new File([req.response], filename, { type: 'application/force-download' });
								window.open(URL.createObjectURL(file));
							}
						}
					};
					req.send(datajson);
                    
                }
            }
		],
		"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
		"order": [[ 0, "desc" ]],
		"pagingType": "full_numbers"
	});
	
	
	
	
	$('#table_org_orders_2').DataTable({
		"dom": 'Bfrtip',
		"buttons": [
			'copyHtml5',
			'excelHtml5',
            {
                text: 'Pdf',
                action: function ( e, dt, button, config ) {
                   
                    //var fstart = $("input[name=start]").val();
                    //var fselected = $('select[name="period"] option:selected').val();
                     
                    var arr = [];
                    
                    //arr.push({"fstart":fstart,"fselected":fselected});
                    arr.push(dt.buttons.exportData());
                     
                    
                     var datajson = JSON.stringify(arr);
                    
                    
					var req = new XMLHttpRequest();
					req.open("POST", "<?=ROOT_PATH; ?>scp/oc_org_fpdf.php", true);
					req.responseType = "blob";
					req.onreadystatechange = function () {
						if (req.readyState === 4 && req.status === 200) {
							
							var filename = "ORDEN_<?= date("Y-m-d")?>.pdf";
							if (typeof window.chrome !== 'undefined') {
								// Chrome version
								var link = document.createElement('a');
								link.href = window.URL.createObjectURL(req.response);
								link.download = "ORDEN_<?= date("Y-m-d")?>.pdf";
								link.click();
							} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
								// IE version
								var blob = new Blob([req.response], { type: 'application/pdf' });
								window.navigator.msSaveBlob(blob, filename);
							} else {
								// Firefox version
								var file = new File([req.response], filename, { type: 'application/force-download' });
								window.open(URL.createObjectURL(file));
							}
						}
					};
					req.send(datajson);
                    
                }
            }
		],
		"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
		"order": [[ 0, "desc" ]],
		"pagingType": "full_numbers"
	});
	
	
	
	$('#table_org_orders_3').DataTable({
		"dom": 'Bfrtip',
		"buttons": [
			'copyHtml5',
			'excelHtml5',
            {
                text: 'Pdf',
                action: function ( e, dt, button, config ) {
                   
                    
                   // var fstart = $("input[name=start]").val();
                  // var fselected = $('select[name="period"] option:selected').val();
                     
                    var arr = [];
                    
                  //  arr.push({"fstart":fstart,"fselected":fselected});
                    arr.push(dt.buttons.exportData());
                     
                    
                     var datajson = JSON.stringify(arr);
                    
                    
					var req = new XMLHttpRequest();
					req.open("POST", "<?=ROOT_PATH; ?>scp/oc_org_fpdf.php", true);
					req.responseType = "blob";
					req.onreadystatechange = function () {
						if (req.readyState === 4 && req.status === 200) {
							
							var filename = "ORDEN_<?= date("Y-m-d")?>.pdf";
							if (typeof window.chrome !== 'undefined') {
								// Chrome version
								var link = document.createElement('a');
								link.href = window.URL.createObjectURL(req.response);
								link.download = "ORDEN_<?= date("Y-m-d")?>.pdf";
								link.click();
							} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
								// IE version
								var blob = new Blob([req.response], { type: 'application/pdf' });
								window.navigator.msSaveBlob(blob, filename);
							} else {
								// Firefox version
								var file = new File([req.response], filename, { type: 'application/force-download' });
								window.open(URL.createObjectURL(file));
							}
						}
					};
					req.send(datajson);
                    
                }
            }
		],
		"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
		"order": [[ 0, "desc" ]],
		"pagingType": "full_numbers"
	});
	
	
	
	
});
</script>
<!-- -------------------------------------------------------------------------------- -->
