        </div>
    </div>
    <div id="footer">
		<?php include ROOT_DIR . 'awesome/inc/client-foot.html'; ?>
    </div>



<div id="espera" class="jconfirm jconfirm-light jconfirm-open" style="display:none">
	<div class="jconfirm-bg" style="transition-duration: 0.4s; transition-timing-function: cubic-bezier(0.36, 0.55, 0.19, 1);">
	</div>
	<div class="jconfirm-scrollpane">
		<div class="jconfirm-row">
			<div class="jconfirm-cell">
				<div class="jconfirm-holder" style="padding-top: 40px;padding-bottom: 40px;">
					<div class="jc-bs3-container container">
						<div class="jc-bs3-row row justify-content-md-center justify-content-sm-center justify-content-xs-center justify-content-lg-center">
							<div class="jconfirm-box-container jconfirm-animated col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1 jconfirm-no-transition" style="transform: translate(0px, 0px); transition-duration: 0.4s; transition-timing-function: cubic-bezier(0.36, 0.55, 0.19, 1);">
								<div class="jconfirm-box jconfirm-hilight-shake jconfirm-type-default jconfirm-type-animated" role="dialog" aria-labelledby="jconfirm-box30526" tabindex="-1" style="transition-duration: 0.4s; transition-timing-function: cubic-bezier(0.36, 0.55, 0.19, 1); transition-property: all, margin; margin-left: 42%;margin-right: 42%;">
									<div class="jconfirm-closeIcon" style="display: none;">×</div>
									<div class="jconfirm-title-c" style="display: none;"><span class="jconfirm-icon-c"></span><span class="jconfirm-title"></span></div>
									<div class="jconfirm-content-pane no-scroll" style="transition-duration: 0.4s; transition-timing-function: cubic-bezier(0.36, 0.55, 0.19, 1); height: 66px; max-height: 477px;">
										<div class="jconfirm-content" id="jconfirm-box30526">
											<div>
												<p class="notice">Procesando....</p>
												<img src="/upload/images/ajax-loader.gif" alt="Procesando">
											</div>
										</div>
									</div>

									<div class="jconfirm-clear"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>













<div id="overlay"></div>
<div id="loading">
	<i class="loading-icon spin"></i>
</div>
<?php
if (($lang = Internationalization::getCurrentLanguage()) && $lang != 'en_US') { ?>
    <script type="text/javascript" src="ajax.php/i18n/<?php
        echo $lang; ?>/js"></script>
<?php } ?>
<script type="text/javascript">
    getConfig().resolve(<?php
        include INCLUDE_DIR . 'ajax.config.php';
        $api = new ConfigAjaxAPI();
        print $api->client(false);
    ?>);
</script>



<!-- agregado por hdandrea 21-03-18 Implementación jquery Data Table    inicio-->
 <script src="<?php echo ROOT_PATH ?>js/jquery.dataTables.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/dataTables.buttons.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/jszip.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/pdfmake.min.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/vfs_fonts.js"></script> 
 <script src="<?php echo ROOT_PATH ?>js/buttons.html5.min.js"></script> 
 <!-- ******************************************************************************* -->
 
 <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/dataTables.checkboxes.min.js"></script>
 
 
<script type="text/javascript">
	
$( document ).ready(function() {
	
	$( "#max" ).datepicker();
	$( "#min" ).datepicker();
	
	 $("#espera").attr("style", "display:none");
	
	var client_panel = document.getElementById("ticketTable2");
	
	
if(client_panel){
	
	$.post( "<?=ROOT_PATH; ?>ajax.mtsolutioncenter.php", { func: "getRole" }).done(function( data ) {
			
		var obj = jQuery.parseJSON(data);
		//alert(obj.role);
		
		if(obj.role == 0){//si no es mananger
			
			$('#ticketTable2 tr').find('td:eq(0),th:eq(0)').remove();
			
			
			
			 $('#ticketTable2').DataTable({
				
				"dom": 'Bfrtip',
				"buttons": [
					'copyHtml5',
					'excelHtml5',
					{
						text: 'Pdf',
						action: function ( e, dt, button, config ) {
						   
							var arr = [];
							
							arr.push(dt.buttons.exportData());
							arr.push({"role":0}); 
							 
							var datajson = JSON.stringify(arr);
							
							var req = new XMLHttpRequest();
							req.open("POST", "<?=ROOT_PATH; ?>scp/oc_cliente_fpdf.php", true);
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
			
			
			
		}else{
			
			
			
			 $('#ticketTable2').DataTable({
				
				"dom": 'Bfrtip',
			   'columnDefs': [
				  {
					 'targets': 0,
					 'checkboxes': {
						'selectRow': true
					 }
				  }
			   ],
		   'select': {
			  'style': 'multi'
		   },
		   'order': [[1, 'asc']],
				"buttons": [
					'copyHtml5',
					'excelHtml5',
					{
						text: 'Pdf',
						action: function ( e, dt, button, config ) {
						   
							var arr = [];
							
							arr.push(dt.buttons.exportData());
							arr.push({"role":1}); 
							var datajson = JSON.stringify(arr);
							
							
							var req = new XMLHttpRequest();
							req.open("POST", "<?=ROOT_PATH; ?>scp/oc_cliente_fpdf.php", true);
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
					},
					{
						text: 'Aprobar realización',
						
						action: function(e, dt, button, config){
							
							
							$.ajaxSetup({
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
							});
							
							var selectedOC = [];
							
							$('#ticketTable2 tbody tr  input:checkbox').each(function(){
								if (this.checked) {
									
									var text = $(this).parent().next('td').text();
									
									selectedOC.push(text);
									
								}
							});
							
							$("#espera").attr("style", "display:block");
							
							
							$.post( "<?=ROOT_PATH; ?>ajax.mtsolutioncenter.php", { func: "updateStatus" , orders: JSON.stringify(selectedOC) }).done(function( data ) {
									
								 var obj = jQuery.parseJSON( data);
								 
								 if(obj.msj != ""){
									 
									 $("#espera").attr("style", "display:none");
									 
								     $.alert({
										title: 'Notificación',
										content: ''+obj.msj+'',
										draggable: true,
										buttons: {
											confirm: {
												text: 'Aceptar',
												btnClass: 'btn-blue',
												action: function () {
													location.reload();
												}
											}
										}
									 }); 									 
									 
								 
								 }
								 	
									
							});
							
							
							
							
						}
					}
					
					
				
					
					
				],
				"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
				"order": [[ 0, "desc" ]],
				"pagingType": "full_numbers"
			});
			
			
			
			
		}
			
			
	},"json");
	
	
	
	
}//endif client
	
	
	
	
	
	$('#ticketTable3').DataTable({
		
		"dom": 'Bfrtip',
		"buttons": [
			'copyHtml5',
			'excelHtml5',
            {
                text: 'Pdf',
                action: function ( e, dt, button, config ) {
                   
                    var arr = [];
                    
                    arr.push(dt.buttons.exportData());
                     
                    var datajson = JSON.stringify(arr);
                    
					var req = new XMLHttpRequest();
					req.open("POST", "<?=ROOT_PATH; ?>scp/oc_cliente_fpdf.php", true);
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


/*    para realizar prueba de comunicación 
$.ajax({
    data: datajson,
    type: "POST",
    dataType: "json",
    url: "<?=ROOT_PATH; ?>scp/oc_cliente_fpdf.php"
})
 .done(function( data, textStatus, jqXHR ) {
     if ( console && console.log ) {
         console.log( "La solicitud se ha completado correctamente." );
     }
     console.log( data );
 })
 .fail(function( jqXHR, textStatus, errorThrown ) {
     if ( console && console.log ) {
         console.log( "La solicitud a fallado: " +  textStatus);
     }
});*/

                }
            }
		],
		"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
		"order": [[ 0, "desc" ]],
		"pagingType": "full_numbers"
	});
	
	
	
	
	
	
	
});


  $('#task_cli').DataTable({
  	"dom": 'Bfrtip',
  	"searching": true,
  	"buttons": [],
  	"pagingType": "full_numbers",
  	"language": {'url': "<?=ROOT_PATH; ?>js/Spanish.lang"},
	"order": [[ 0, "desc" ]]
  });

</script>
<!--  ************* fin ********************************************* -->


<script>
	
$("#opened").click(function(){
	document.getElementById('opened').className = 'active';
	document.getElementById('closed').className = 'inactive';
	document.getElementById('cont_opened').className = 'tab_content';
	document.getElementById('cont_closed').className = 'tab_content hidden';
});

$("#closed").click(function(){
	
	document.getElementById('opened').className = 'inactive';
	document.getElementById('closed').className = 'active';
	document.getElementById('cont_closed').className = 'tab_content';
	document.getElementById('cont_opened').className = 'tab_content hidden';
});

</script>


</body>
</html>
