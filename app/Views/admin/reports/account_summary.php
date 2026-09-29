<?php $header = array( 	'title' => 'Accounts Summary' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
    input.custom-combobox-input{width:100%;}
</style>
 <form class="myform needs-validation" method="post" id="salefrm"  novalidate>
<div class=" row">
                
             <div class="col-6"><h3 class="pb-3">Account Summary</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></span></div> 
             <div class="col-md-6 card p-3 m-auto shadow-lg mt-3">
                <h5 class="pb-5">Financial Year 2023 - 24</h5>
                
              <div class="col-12">
                  <label>Summary Type</label>
                  <select id="summary_type" name="summary_type" class="form-control">
                        <option value=""></option>
                      <?php foreach($summary_detail_dropdown as $key => $value){ ?>
                        <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
                      <?php } ?>
                  </select>  
                   
               </div>
               <div class="col-12">
                  <label>Detail</label>
                  <select id="summary_detail" name="summary_detail" class="form-control" disabled>
                  </select>  
                   
               </div>
                
                <div class="col-md-12 mt-2">
                    <label>Account</label>
					<input type="text" name="item"  class="form-control borderdark"  placeholder="Search name...">
                    <input type="hidden" name="id" value="">
                </div>               
            
             <div class="col-md-12 text-center  my-3">
                 <input type="submit" value="GO" class="btn btn-success mr-1" fdprocessedid="vqsefp">
                 <input type="reset" value="QUIT" class="btn btn-secondary">
              </div>
            
            </div>
             </div>
               
 </form>
          
          
  <?php echo view('includes/footer_scripts'); ?>
  
  <script>
    var arr = <?php echo html_entity_decode(json_encode($summary_detail_dropdown)) ?>;      
   var accounts_list = <?php echo $accounts_list ?>;
    var groups_list = [];<?php //echo html_entity_decode(json_encode($groups_list)) ?>;
    var bill_sundry_list = [];<?php //echo html_entity_decode(json_encode($bill_sundry_list)) ?>;
    var cc_list = [];<?php //echo html_entity_decode(json_encode($cc_list)) ?>;
    var cc_groups = [];<?php //echo html_entity_decode(json_encode($cc_groups)) ?>;
        getList(accounts_list); 
      $('#summary_type').change(function(){
          var value = $(this).val();

          $('select[name="summary_detail"]').html('').attr('disabled','disabled');
          $('input[name="item"]').val('').attr('disabled', 'disabled');
          $('input[name="id"]').val('');
          
         if(value != "")
         {
            $.each(arr, function( index, val ) {
                if(index == value){
                    var html = `<option value=""></option>`;
                    $.each(val, function(i,v){
                        html += `<option value="${v}">${v}</option>`;
                    })
                    $('[name="summary_detail"]').html(html).attr('disabled',false);
                }
            });
         }
      });

    $('#summary_detail').change(function(){
        var detail = $(this).val();
        var type = $('[name="summary_type"]').val();
        
        $('input[name="item"]').val('').attr('disabled', 'disabled');
        $('input[name="id"]').val('');

        if(type == 'Account Summary')
        {
            if(detail == 'Ledger')
            {
              
                $('input[name="item"]').attr('disabled', false);
            }
            if(detail == 'Account Group')
            {
                getList(groups_list);
                $('input[name="item"]').attr('disabled', false);
            }
        }
        if(type == 'Cost Centre Summary')
        {
            if(detail == 'Cost Centre')
            {
                getList(cc_list);
                $('input[name="item"]').attr('disabled', false);
            }
            if(detail == 'Cost Centre Group')
            {
                getList(cc_groups);
                $('input[name="item"]').attr('disabled', false);
            }
        }
        if(type == 'Bill Sundry Summary')
        {
            if(detail == 'Account')
            {
                getList(bill_sundry_list);
                $('input[name="item"]').attr('disabled', false);
            }
        }
    });
    function getList(list) 
    {
        $('input[name="item"]').off('blur'); // unbind event first
        
        if($('input[name="item"]').hasClass('ui-autocomplete-input')) {
            $('input[name="item"]').autocomplete("destroy");
        }
        
        $( 'input[name="item"]' ).autocomplete({
            source: list,
            minLength: 0,
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                $('input[name="id"]').val(ui.item.id);
            }
        })
        .on('focus', function(){
            $(this).autocomplete( "search", "" );
            $('input[name="item"]').val('');
            $('input[name="id"]').val('');
        })
        .on('blur', function(){
            
            if($(this).val() != '' && $('input[name="id"]').val() == '')
            {
                var acc = $(this).val();
                var index = list.findIndex(function(obj) {
                    var string = obj.label.toLowerCase();
                    var text = acc.toLowerCase();
                   return  string.includes(text);
                });
                if(index > -1){
                    $(this).val(list[index].label);
                    $('input[name="id"]').val(list[index].id);
                    
                }
                else{
                    $('input[name="item"]').val('');
                    $('input[name="id"]').val('');
                }
            }
            if($('input[name="id"]').val() == '')
            {
                $('input[name="item"]').val('');
                $('input[name="id"]').val('');
            }
        });
    }

    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        window.location.reload();
    }

  </script>

 </body>
</html>
