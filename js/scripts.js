$(document).ready(function() {

  var customerList = new Array();
  var customerID = new Array();
  var customerRegDate = new Array();

  var customer_item_arr = new Array();
  var consultant_item_arr = new Array();

  var timeline_filters = ['cd-purchase','cd-repair','cd-books-parts'];

  //ON LOAD
  $(function(){

    $(".serial-number").hide();
    $("#customer_tel_no").mask("000-000-0000");

    $.widget( "custom.catcomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
      },
      _renderMenu: function( ul, items ) {
        var that = this,
          currentCategory = "";
          console.log(items);
        $.each( items, function( index, item ) {
          var li;
          if(item.flag == "cust") {
            item.category = "CUSTOMERS";
          } else if(item.flag == "user") {
            item.category = "USERS";
          } else {
            item.category = "NO MATCH FOUND";
          }
          if ( item.flag != currentCategory ) {
            ul.append( '<li><div class="ui-autocomplete-category" style="text-align:center"><font color="grey">'+item.category+'</font></div></li>' );
            currentCategory = item.flag;
          }
          li = that._renderItemData( ul, item );
          if ( item.category ) {
            li.attr( "aria-label", item.flag + " : " + item.label );
          }
        });
      }
    });

    $('body').on('keypress','#main-search',function(){
      $('#main-search').siblings('span.input-group-btn').find('i.fa-search').removeClass('fa-search').addClass('fa-refresh fa-spin');
    }); 

    $( "#main-search" ).catcomplete({
      source: function( request, response ) {
          $.ajax( {
            type: 'POST',
            url: 'ajax/get_persons.php',  
            data: JSON.stringify({name: $( "#main-search" ).val()}),
            dataType: 'JSON',
            success: function( data ) {

              if(!data.success) {
                toastr.error(data.message);
                return;
              }
              if(data.response == 0) {
                var result = [{
                   label: 'RECORD DOES NOT EXIST', 
                   value: response.term,
                 }];
                response(result);
              } else {
                var searchResults = [];
                $.each(data.response,function(){
                  searchResults.push({
                        label: $(this)[0].full_name, 
                        address: $(this)[0].address,
                        city: $(this)[0].city,
                        country: $(this)[0].country,
                        email: $(this)[0].email,
                        first_name: $(this)[0].first_name,
                        flag: $(this)[0].flag,
                        id: $(this)[0].id, 
                        last_name: $(this)[0].last_name,
                        register_date: $(this)[0].register_date,
                        tel_no: $(this)[0].tel_no,
                        title: $(this)[0].title,
                        user_type: $(this)[0].user_type,
                        full_name: $(this)[0].full_name  
                      });
                });
                response(searchResults);
            } 
            $('#main-search').siblings('span.input-group-btn').find('i.fa-refresh.fa-spin').removeClass('fa-refresh fa-spin').addClass('fa-search');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
              console.log("ERROR");
              return false;
            }
          });
        },
        minLength: 2,
        select: function( event, ui ) {
          $('#main-search').val("");
          var personObj = ui.item;
          if(personObj.id == null || personObj.id == 'undefined') {
            return false;
          }

          $.fancybox.open({
              padding: 20,
              minWidth:'20%',
              height: '60%',
              minHeight:'20%',
              type: 'ajax',
              href: 'views/confirm_person.php',
              modal: true,
              type: "ajax",
              ajax: {
                  type: "POST",
                  data: JSON.stringify(personObj),
              },
              helpers: { 
                  overlay: { 
                      locked: false 
                  } 
              }
            }); 
        }
      });


  });

  //CLICK/CHANGE EVENTS

  $('body').on('change','.cmn-toggle',function(){
    var filter = $(this).prop("id");
    filter = filter.replace("_timeline_filter","");

    if($(this).is(":checked")) {
      $("."+filter).show(500);
      if(jQuery.inArray(filter,timeline_filters) == -1) {
        timeline_filters.push(filter);
      }
    } else {
      if(timeline_filters.length == 1) {
        bootbox.alert("You must select atleast one filter!");
        $(this).prop('checked',true);
        return false;
      }

      $("."+filter).hide(500);
      var index = timeline_filters.indexOf(filter);
      timeline_filters.splice(index,1);
    }
  });

  $('body').on('click','.show-hide-panel',function(){
      $(this).parent().parent().next().slideToggle();
      $(this).toggleClass('glyphicon glyphicon-minus').toggleClass('glyphicon glyphicon-plus');
      $.fancybox.update();
  });

  $('body').on('keyUp','#main-search',function(){
  
    $('#btn-tm5-search').children().toggleClass('glyphicon glyphicon-refresh glyphicon-refresh-animate').toggleClass('glyphicon glyphicon-search');
  });

  $('body').on('click','.correct-person-search',function(){
    
    var obj = {};
    var objLinks = [];

    //FOR NOTIFICATIONS
    var personId = $(this).attr('data-person-id');
    var action = $(this).attr('data-action');
    var activityId = $(this).attr('data-activity-id');
    var userId = $(this).attr('data-user-id');
    var notificationId = $(this).attr('data-notification-id');
    var customerActivityID = $(this).attr('customer-activity-id');


    $.fancybox.close();

    if(userId != 'undefined' && userId != '' && userId != null) {
      userId = '&uid='+userId;
    } else {
      userId = '';
    }

    if(activityId != 'undefined' && activityId != '' && activityId != null) {
      activityId = '&acid='+activityId;
    } else {
      activityId = '';
    }

    if(action != 'undefined' && action != '' && action != null) {
      action = '&action='+action;
    } else {
      action = '';
    }

    if(notificationId != 'undefined' && notificationId != '' && notificationId != null) {
      notificationId = '&nid='+notificationId;
    } else {
      notificationId = '';
    }

    if(action != 'undefined' && action != null && action != '') {
      if(activityId != 'undefined' && activityId != null && activityId != '') {
        obj.href="timeline";
        objLinks.push(action,activityId,userId,notificationId);
        obj.objLinks = objLinks;
        // window.location.href= 'index.php?view=timeline&action='+action+'&acid='+activityId+userId+notificationId;
      } else {
        obj.href="timeline";
        // obj.
        window.location.href= 'index.php?view=timeline&action='+action;
      }
    } else {      
      obj.href="timeline";
      // hyperlinkObj(obj);
    }

    hyperlinkObj(obj);
   
  });


  $('body').on('click','.incorrect-person-search',function(){
    $.fancybox.close();
  });


  $('body').on('click','.generate-invoice',function(){

    check_session();
  
    var data = {};
    data.customer_id = $(this).data('customer-id');
    data.customer_activity_id = $(this).data('customer-activity-id');

    generateCustomerInvoice(data,null);
  });

  $('body').on('click','#add-customer-activity',function(){
    data = {};
    data.action = 'add-activity';

    check_session();

    $.fancybox.open({
        padding: 0,
        minWidth:'20%',
        minHeight:'20%',
        type: 'ajax',
        href: 'views/add_customer.php',
        modal: false,
        type: "ajax",
        ajax: {
            type: "POST",
            data: JSON.stringify(data),
        },
        helpers: { 
          overlay: { 
              locked: false 
          } 
        }
    });
  });

  $('body').on('click','#edit-consultant',function(){
    check_session();

    var consultantID = $(this).data("id");

    $.fancybox.open({
      padding: 0,
      minWidth:'20%',
      minHeight:'20%',
      type: 'ajax',
      href: 'views/edit_consultant.php?id='+consultantID,
      modal: false,
      helpers: { 
        overlay: { 
            locked: false 
        } 
      }
    });
  });

   $('body').on('click','#edit-customer',function(){
    check_session();

    var customerID = $(this).data("id");

    $.fancybox.open({
      padding: 0,
      minWidth:'20%',
      minHeight:'20%',
      type: 'ajax',
      href: 'views/edit_customer.php?id='+customerID,
      modal: false,
      helpers: { 
          overlay: { 
              locked: false 
          } 
      }
    });
  });

  $('body').on('click','#save-edit',function(){
      var o = {};
      var a = $('#edit-customer-form').serializeArray();

      $.each(a,function(){
        o[$(this)[0].name] = $(this)[0].value.replace(/["]/g,'&quot;').replace(/[\<]/g,'&lt;').replace(/[\>]/g,'&gt;') || '';
      });

      console.log(o);

      $.ajax({
        type: 'POST',
        url: 'ajax/insertupdate_customer_details.php',
        dataType: 'text',
        data: JSON.stringify(o),
        dataType: 'JSON',
        success: function( data ) {

          if(!data.success) {
            toastr.error(data.message);
            return;
          }
          $.fancybox.close();
          toastr.success("Customer details changed");
          setTimeout(function(){window.location.reload();}, 2000);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("ERROR");
          return false;
        }         
      });
  });

  $('body').on('click','#restore-details',function(){
    // location.reload();  
    alert('todo');
  });


  $('body').on('click','#main-search', function(){
    $(this).val("");
  });

  $('body').on('click','#reports-dialogue',function(){
     check_session();

    $.fancybox.open({
      padding: 0,
      minWidth:'90%',
      minHeight:'90%',
      type: 'ajax',
      href: 'views/reports.php',
      modal: false,
      helpers: { 
        overlay: { 
            locked: false 
        } 
      }
    });
  });

  $('body').on('click', '#add-customer-dialogue', function(){
    check_session();

    $.fancybox.open({
      padding: 0,
      minWidth:'20%',
      minHeight:'20%',
      type: 'ajax',
      href: 'views/add_customer.php',
      modal: true
      // helpers: { 
      //   overlay: { 
      //       locked: false 
      //   } 
      // }
    });
  });

  $('body').on('click','.activate-account', function() {

      var form = '#activate_account';

      if(regformhashPassword($(form),$(form+' input[name="identifier"]'),$(form+' input[name="activate_password"]'),
        $('input[name="activate_password_confirm"]'))) {

        var data  = $('#activate_account').serializeArray();

        $.blockUI({ message: '<h1> Activating User</h1>' });

        $.ajax({
          type: 'POST',
          url: '../ajax/activate_user.php',
          data: data,
          dataType: 'JSON',
          success: function(data) {
            if(data.success) {
              $(form).trigger("reset");
              $.unblockUI();
              bootbox.alert({
                title: "<span class='glyphicon glyphicon-ok'></span> Account Registered",
                message: "Press OK To Login",
                callback: function (result) {
                  window.location.href = 'index.php';
                }
              });
            } else {
              $.unblockUI();
              toastr.error(data.message);
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            toastr.error("SERVER REQUEST ERROR");
            return false;
          }
        });
      } 
  });

  $('body').on('click','#forgot-password',function(){
    $('#error').text("");
    $('label[for=username], input#username').hide();
    $('label[for=password], input#password').hide();
    $('label[for=email], input#email').show();
    $('.login').hide();
    $('.send-password-email').show();
    $('#forgot-password').hide();
    $('.message').text("Please enter the email you are registered to for a password reset.");
  });

  $('body').on('click','.send-password-email',function(){
    $('#error').text("");

    $.blockUI({ message: '<h1> Sending Email</h1>' });
    var data = {};
    data.email = $('#email').val();
    
    $.ajax({
        type: 'POST',
        url: 'ajax/send_user_password_reset.php',
        dataType: 'JSON',
        data: JSON.stringify(data),
        success: function( data ) {
          if(data.success){
            $.unblockUI();
            $('label[for=email], input#email').hide();
            $('.send-password-email').hide();
            $('.message').text("The email has been sent to your inbox");
          } else {
            $.unblockUI();
            $('#error').text(data.message);
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("error with invoice");
          return false;
        }
    });
  });

  $('body').on('click','#email',function(){
    $(this).val("");  
  });

  $('body').on('click','.reset-password',function(){
    var form = '#reset_password';

    if(regformhashPassword($(form),$(form+' input[name="identifier"]'),$(form+' input[name="activate_password"]'),
      $(form+' input[name="activate_password_confirm"]'))) {

      var data  = $('#reset_password').serializeArray();

      $.blockUI({ message: '<h1> Resetting Password</h1>' });

      $.ajax({
        type: 'POST',
        url: '../ajax/activate_user.php',
        data: data,
        dataType: 'JSON',
        success: function(data) {
          if(data.success) {
            $(form).trigger("reset");
            $.unblockUI();
            bootbox.alert({
              title: "<span class='glyphicon glyphicon-ok'></span> Password Reset",
              message: "Press OK To Login",
              callback: function (result) {
                window.location.href = 'index.php';
              }
            });
          } else {
            $.unblockUI();
            toastr.error(data.message);
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          toastr.error("SERVER REQUEST ERROR");
          return false;
        }
      });
    } 
  });

  $('body').on('click', '#cancel-fancybox', function(){

    check_session();

    $.fancybox.close();0
  });

  $('body').on('click','.login',function(){

    $('#error').text("");
    form = '#login';
   
    if(formhash($(form),$(form+' #password'))) {

        var data  = $(form).serializeArray();

        $.ajax({
          type: 'POST',
          url: '../ajax/process_login.php',
          data: data,
          dataType: 'JSON',
          success: function(data) {
            if(data.success) {
              $(form).trigger("reset");
              window.location.href="index.php?view=timeline&type=muser";
            } else {
              $('#error').text(data.response);
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            toastr.error("SERVER REQUEST ERROR");
            return false;
          }
        });
    } 

  });


  $('body').on('click', '#logout', function(){

     $.ajax({
            type: 'post',
            url: 'ajax/logout.php',
            success: function( data ) { 
              window.location.href = 'index.php';
            }
    });
  });

  $('body').on('click','#clear',function(){

    //remove all classes
    $('tr').removeClass('success');
    $('tr').removeClass('warning');
    $('tr').removeClass('info');

    //uncheck all checkboxes
    $(':checkbox').prop('checked', false);
    $(':checkbox').removeClass('checked');

    //clear the arrays
    checkedParts = [];
    checkedPartsQuantity = [];

    //scroll to top
    $("html, body").animate({ scrollTop: 0 }, "fast");

    //hide buttons
    $('#confirm').show();

    //clear quantities
    $('input[type=number].checked').each(function(){
      $(this).val(0);
    });

  });

  $('body').on('click','#stats-view',function(){

      check_session();

      $.blockUI({ message: '<h1> Loading Statistics View</h1>' });

      var id = $(this).data("id");

      if(id == "undefined" || id == null || id == "") {
        id = null;
      }

      $.ajax({
        type: 'POST',
        url: 'ajax/get_cons_stats.php',
        dataType: 'JSON',
        data: {"id" : id},
        success: function(data) { 

          if(!data.success) {
            toastr.error(data.message);
            return;
          }

          if(data.response == "No stock") {
            $.unblockUI();
            bootbox.alert(data.message);
            return;
          }

          // SWITCH BUTTONS
          $('#stats-view-container').addClass('hidden');
          $('#timeline-view-container').removeClass('hidden');

          //SWITCH TOGGLES
          $('.checkbox-switch').hide();
          $('.checkbox-switch').prev().hide();

          //SWITCH HEADINGS
          $('#consultant-heading').addClass('hidden');

          $.unblockUI();

          // SHOW CORRECT CONTAINERS
          $('#cons-stock-container').removeClass('hidden');
          $('#timeline-container').addClass('hidden');
        
          createConsultantStockTable(data.response);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
              toastr.error("ERROR");
              return false;
        }
    });
  });

  $('body').on('click','.view-activity-details',function(){

      check_session();

      var data = $(this).data('customer-activity-id');

      $.fancybox.open({
          padding: 0,
          minWidth:'10%',
          minHeight:'10%',
          type: 'ajax',
          href: 'views/activity_details.php',
          modal: false,
          type: "ajax",
          ajax: {
              type: "POST",
              data: JSON.stringify(data),
          },
          helpers: { 
            overlay: { 
                locked: false 
            } 
          }
      });
  });

  $('body').on('click','#timeline-view',function(){
    check_session();

    // EMPTY TIMELINE CONTAINER
    $('.timeline').empty();

    //SHOW CORRECT CONTAINERS
    $('#cons-stock-container').addClass('hidden');
    $('#timeline-container').removeClass('hidden');

    // SWITCH BUTTONS
    $('#stats-view-container').removeClass('hidden');
    $('#timeline-view-container').addClass('hidden');

    //SWITCH TOGGLES
    $('.checkbox-switch').show();
    $('.checkbox-switch').prev().show();

    //SWITCH HEADINGS
    $('#consultant-heading').removeClass('hidden');

    getTimeline($(this).data("type"));
  });


  //ADD CONSULTANT

  $('body').on('keypress','#item-search-cons',function(){

    $("#add-quantity-cons").attr({
       "max" : "",        
       "min" : "0"          
    });

    $.ajax({
        type: 'POST',
        url: 'ajax/get_user_stock_items.php',
        data: {
          search_val: $('#item-search-cons').val()
        },
        dataType: 'JSON',
        success: function(data) { 

          if(!data.success) {
            toastr.error(data.message);
            return;
          }

          itemList = data.response;
            
          $( "#item-search-cons" ).autocomplete({
              source: function (request, response) {
                response($.map(itemList, function (value, key) {
                  return {
                      label: value.material_name,
                      value: value.material_name,
                      val: value.quantity
                  }
                }));
              },
              delay: 0,
              autoFocus: true,
              focus: function( event, ui ) { event.preventDefault(); },
                // minLength: 2,
              select: function(e, ui) {     
                var max = AutoCompleteSelectHandler(e, ui);
                $("#add-quantity-cons").attr({
                   "max" : max,        
                   "min" : "0"          
                });
              }
            });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          toastr.error("ERROR");
          return false;
        }
    });
  });


  $('body').on('click', '#add-item-cons', function() {
  
    if(parseInt($('#add-quantity-cons').val()) > parseInt($('#add-quantity-cons').attr("max"))) {
      toastr.error('Not enough stock for: '+ $('#item-search-cons').val());
      return;
    }

    var index = 0;
    var obj = {};

    check_session();

    $.blockUI({ message: '<h1> fetching items</h1>' });

    $.ajax({
      type: 'POST',
      url: 'ajax/get_items_codes.php',
      data: {
        material_name: $('#item-search-cons').val(),
      },
      dataType: 'JSON',
      success: function(data) {

        if(!data.success) {
          toastr.error(data.message);
          return;
        }

        $.unblockUI();  

        if($('#add-quantity-cons').val() == "")  {
          toastr.error("No Quantity selected");
          $('#add-quantity-cons').addClass('error-radcheck');
          return;
        } else {
          $('#add-quantity-cons').removeClass('error-radcheck');
        }

        if(data.response == null){
          toastr.error("Item does not exist");
        } else {
          toastr.success("Item added");

            obj.itemName = $('#item-search-cons').val();
            obj.itemQuantity = $('#add-quantity-cons').val();
            obj.itemCode = data.response;
            obj.maxItemQuantity = parseInt($('#add-quantity').attr("max"));

            consultant_item_arr.push(obj);
            index = consultant_item_arr.length - 1;

            $('#item-table-cons').append("<tr data-id='"+index+"'><td><a id='remove-item'><span class='glyphicon glyphicon-trash hoverable'></span></a></td><td>"+data.response+"</td><td>"+ $('#item-search-cons').val()+"</td><td>"+$('#add-quantity-cons').val()+"</td></tr>");
        }
        
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
        toastr.error("ERROR");
        return false;
      }
    });

  });

  $('body').on('change','input[name="user_type_radio"]',function(){
    if($(this).val() == 3) {
      $(this).parent().parent().parent().parent().removeClass('col-md-12').removeClass('col-xs-12').addClass('col-md-6').addClass('col-xs-6');
      $('#parent_user_cont').removeClass("hidden");
    } else {
      $('#parent_user').val("");
      $(this).parent().parent().parent().parent().removeClass('col-md-6').removeClass('col-xs-6').addClass('col-md-12').addClass('col-xs-12');
      $('#parent_user_cont').addClass("hidden");
    }
  });

  $('body').on('focusout','#add_user_form input[name="first_name"]',function(){
    $('#add_user_form input[name="user_name"]').val($(this).val().charAt(0).toLowerCase() + $('#add_user_form input[name="last_name"]').val().toLowerCase());
  }); 

  $('body').on('focusout','#add_user_form input[name="last_name"]',function(){
    $('#add_user_form input[name="user_name"]').val($('#add_user_form input[name="first_name"]').val().charAt(0).toLowerCase() + $(this).val().toLowerCase());
  });

  $('body').on('click','#refresh-user-form',function(){
    var form = '#add_user_form';
    $(form).trigger("reset");
  });

  $('body').on('click', '#add-user',function(){
      check_session();

      var valid = true;
      var form = '#add_user_form';

      if(checkFormValid(form) && regformhash($(form),$(form+' input[name="user_name"]'))) {

        var data  = $('#add_user_form').serializeArray();

        $.blockUI({ message: '<h1> Adding User</h1>' });

        $.ajax({
          type: 'POST',
          url: '../ajax/insert_user.php',
          data: data,
          dataType: 'JSON',
          success: function(data) {
            if(data.success) {
              $(form).trigger("reset");
              $.unblockUI();
              toastr.success("User added!");
              setTimeout(function(){window.location.reload()}, 1000);
            } else {
              $.unblockUI();
              bootbox.alert(data.message);
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            toastr.error("SERVER REQUEST ERROR");
            return false;
          }
        });
      } else {

      }
  });

  $('body').on('click','#item-search-cons',function(){
    $(this).val("");
    $('#add-quantity-cons').val("");
  });


  $('body').on('click','#add-consultant-inventory-dialogue',function() {

      check_session();

      consultant_item_arr = []; //CLEAR THE ARRAY

      $.fancybox.open({
        padding: 0,
        minWidth:'20%',
        minHeight:'20%',
        type: 'ajax',
        href: 'views/add_consultant_inventory.php',
        modal: false,
        helpers: { 
            overlay: { 
                locked: false 
            } 
        }
      });

  });

  $('body').on('click','#add-consultant-host-gift-dialogue',function() {

      check_session();

      consultant_item_arr = []; //CLEAR THE ARRAY

      $.fancybox.open({
        padding: 0,
        minWidth:'20%',
        minHeight:'20%',
        type: 'ajax',
        href: 'views/add_host_gift.php',
        modal: false,
        helpers: { 
          overlay: { 
              locked: false 
          } 
        }
      });

  });

  $('body').on('click','#add-gift-cons',function(){
    
    var index = 0;
    
    obj = {itemType: "", itemName: "", itemQuantity: "", itemCode: ""};

    check_session();

    $.blockUI({ message: '<h1> fetching items</h1>' });

      $.ajax({
        type: 'POST',
        url: 'ajax/get_items_codes.php',
        data: {
          material_name: $('#item-search-cons').val(),
        },
        dataType: 'JSON',
        success: function(data) {

          if(!data.success) {
              toastr.error(data.message);
              return;
            }

          $.unblockUI();  

          if($('#add-quantity-cons').val() == "")  {
            toastr.error("No Quantity selected");
            return;
          }  

          if(data.response == null){
            toastr.error("Item does not exist");
          } else {
            toastr.success("Item added");

              obj.itemType = $('#select_gift_option').val();
              obj.itemName = $('#item-search-cons').val();
              obj.itemQuantity = $('#add-quantity-cons').val();
              obj.itemCode = data.response;

              consultant_item_arr.push(obj);
              index = consultant_item_arr.indexOf(obj);

              $('#item-table-cons').append("<tr data-id='"+index+"'><td><a id='remove-item'><span class='glyphicon glyphicon-trash hoverable'></span></a></td><td>"+$('#select_gift_option').val()+"</td><td>"+data.response+"</td><td>"+ $('#item-search-cons').val()+"</td><td>"+$('#add-quantity-cons').val()+"</td></tr>");
          }
          
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
              toastr.error("ERROR");
              return false;
        }
      });
  });

  $('body').on('click','#add-consultant-host-gift',function(){

      var quantity = "";   
      consultantID = $(this).data('consultant-id');

      if($('#add-quantity-cons').val() == "")  {
        toastr.error("No Quantity selected");
        return;
      }  

      addConsultantGift(consultantID,consultant_item_arr);

      toastr.success("Gift Added!");

      $.fancybox.close();

      consultant_item_arr = [];
  });


  $('body').on('click','#add-consultant-inventory',function(){

    // console.log(consultant_item_arr);
    // return false;

      check_session();

      var quantity = "";   
      var userID = $(this).data('user-id');

      if(consultant_item_arr.length == 0)  {
        toastr.error("You have selected added any items");
        return;
      }  

      addConsultantStock(userID,consultant_item_arr);

      toastr.success("Inventory Added!");

      $.fancybox.close();

      $.each(consultant_item_arr,function(){
        if($(this)[0].itemCode == "1000") {
          quantity = $(this)[0].itemQuantity;

          $.fancybox.open({
            padding: 0,
            minWidth:'10%',
            minHeight:'10%',
            type: 'ajax',
            href: 'views/add_tm_invoice_nos.php',
            modal: true,
            type: "ajax",
            ajax: {
                type: "POST",
                data: {"quantity": quantity, "user_id": userID},
            },
            helpers: { 
              overlay: { 
                locked: false 
              } 
            }
        });
        }
      });

      consultant_item_arr = [];
  });

  $('body').on('click','#add-tm-serial-number',function(){
    var valid = true;
    var obj = {};
    var objLinks = [];
    var customerActivityID = $(this).data('customer-activity-id');
    var notificationID = $(this).data('notification-id');
    var o = {};
    var a = $('#add-tm-serial').serializeArray();

    $('.valid').each(function(){
      if($(this).val() == "") {
        $(this).addClass('error-radcheck');
        valid = false;
        return false;
      } else {
        $(this).removeClass('error-radcheck');
      }
    });

    if(valid) {

      $.blockUI({ message: '<h1> Adding Serial Numbers</h1>' });

      $.each(a,function(){
        o[$(this)[0].name] = $(this)[0].value;
      });

      $.ajax({
          type: 'POST',
          url: 'ajax/add_tm_serials.php',
          data: {
            customer_activity_id: customerActivityID,
            notification_id: notificationID,
            serial_nos: o
          },
          dataType: 'JSON',
          success: function(data) {
            if(data.success) {
              $.unblockUI();
              $.fancybox.close();
              toastr.success("Serial numbers added!");
              obj.href = "timeline";
              objLinks.push("&type=muser");
              obj.objLinks = objLinks;
              setTimeout(function(){hyperlinkObj(obj); }, 2000);
            } else {
              toastr.error(data.message);
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            toastr.error("SERVER REQUEST ERROR");
            return false;
          }
      });
    }
  });

  $('body').on('click','#add-tm-invoice-num',function(){
    var valid = true;
    var userID = $(this).data('user-id');
    
    var o = {};
    var a = $('#thermo_invoice_nos').serializeArray();

    $.each(a,function(){
      o[$(this)[0].name] = $(this)[0].value;
    });

    $('.invoice_nos').each(function(){
      if($(this).val() == "") {
        $(this).addClass('error-radcheck');
        valid =  false;
      } else {
        $(this).removeClass('error-radcheck');
      }
    });

    if(valid) {
        $.blockUI({ message: '<h1> Adding Invoice Numbers</h1>' });

        $.ajax({
            type: 'POST',
            url: 'ajax/add_tm_invoice_numbers.php',
            data: {
              user_id: userID,
              invoice_nos: o,
            },
            dataType: 'JSON',
            success: function(data) {
              if(data.success) {
                $.unblockUI();
                toastr.success("Invoice numbers added!");
                $.fancybox.close();
              } else {
                toastr.error(data.message);
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
              toastr.error("SERVER REQUEST ERROR");
              return false;
            }
        });
    }
  });


  //ADD CUSTOMER 

  $('body').on('change', '#user', function(){
    $('input[name="customer_activity"]').trigger("change");
  });

  $('body').on('change','input[name="customer_activity"]',function(){

    // var originalElem = '<label class="control-label for="invoice_number">Invoice Number</label>';
    // originalElem += '<input type="text" name="invoice_number" id="invoice_number" class="form-control mandatory-field"/>';

    var replaceElem = '';

    if($(this).val() == "") {
      return false;
    }

    $('#item-search').val("");
    $('#add-quantity').val("");
    $('#add-tm-serial').val("");

    customer_item_arr = [];
    $('#item-table tr').each(function(){
      if($(this).attr('id') != 'header') {
        $(this).empty();
      }
    }); 

    if($('#has_serial').val() == 1) {
      if($(this).val() == "Repaired Machine Not Under Warranty" || $(this).val() == "Repaired Machine Under Warranty") {
        $('#add-tm-serial').parent().hide();
        $('#serial_number_select').parent().show();
      } else {
        $('#add-tm-serial').parent().show();
        $('#serial_number_select').parent().hide();
      }
    }

    if($(this).val() == "TM5 Domestic Purchase" || $(this).val() == "TM5 Commercial Purchase") {
      
      //ADMINS MUST ENTER SERIALS
      var obj = addTMPurchase();
      if($('#user_type').val() == 1) { 
        $('#item-search').val(obj.itemName);
        $('#add-quantity').val(obj.itemQuantity);
        $('#item-search').parent().removeClass('col-md-6').removeClass('col-sm-6').addClass('col-sm-3').addClass('col-md-3');
        $('#add-tm-serial').parent().show();
        $('#add-tm-serial').prop('disabled',false);
      } else {
        customer_item_arr.push(obj);
        index = customer_item_arr.length - 1;
        $('#item-table').append("<tr data-id='"+index+"'><td><a id='remove-item'><span class='glyphicon glyphicon-trash hoverable'></span></a></td><td>"+obj.itemCode+"</td><td>"+ obj.itemName+"</td><td>"+obj.itemQuantity+"</td></tr>");
      }

      $.ajax({
            type: 'POST',
            url: '../ajax/get_tm_invoice_nos.php',
            data: {
              "user_id" : $('#user').val(),
            },
            dataType: 'JSON',
            success: function(data) {
              if(data.success) {
                if(data.response == 0) {
                  $('#invoice_number_cont').html("");
                } else {
                  replaceElem = '<div class="form-group"><label for="invoice_number">Invoice Number</label>';
                  replaceElem += '<select class="form-control input-sm valid" name="invoice_number" id="invoice_number">';
                  $.each(data.response, function(){
                    replaceElem += '<option value="'+$(this)[0].invoice_number+'">'+$(this)[0].invoice_number+'</option>';
                  });
                  replaceElem += '</select></div>';
                  $('#invoice_number_cont').html(replaceElem);
                }
              } else {
                toastr.error(data.message);
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
              toastr.error("SERVER REQUEST ERROR");
              return false;
            }
        });
    } else {
      $('#invoice_number_cont').html("");
      $('#item-search').parent().removeClass('col-md-3').removeClass('col-sm-3').addClass('col-sm-6').addClass('col-md-6');
      $('#add-tm-serial').parent().hide();
      $('#add-tm-serial').prop('disabled',true);
    }

    switch($(this).val()) {
      case "TM5 Domestic Purchase":
        $('#activity_type').val('TM Purchase');
        break;
      case "TM5 Commercial Purchase":
        $('#activity_type').val('TM Purchase');
        break;
      case "Repaired Machine Not Under Warranty":
        $('#activity_type').val('Repair');
        break;
      case "Repaired Machine Under Warranty":
        $('#activity_type').val('Repair');
        break;
      case "Books/Parts Purchase":
        $('#activity_type').val('BP Purchase');
        break;
    } 
  });

  $('body').on('click','#item-search',function(){
    $(this).val("");
    $('#add-quantity').val("");
  });

  // var tmSerial;

  $('body').on('click', '#add-item', function() {

    //CHECK STOCK
    if(parseInt($('#add-quantity').val()) > parseInt($('#add-quantity').attr("max"))) {
      toastr.error('Not enough stock for: '+ $('#item-search').val());
      return;
    }

    // IF SERIAL VISIBILE (ADMIN)
    if($('#add-tm-serial').is(":visible")) {
      if($('#add-tm-serial').val() == "") {
        $('#add-tm-serial').addClass('error-radcheck');
        toastr.error("Please add a serial number");
        return false;
      } else {
        $('#add-tm-serial').removeClass('error-radcheck');
      }
    } 

    var index = 0;
    // obj = {itemName: "", itemQuantity: "", itemCode: "", itemSerial: ""};
    var obj = {};

    check_session();

    $.blockUI({ message: '<h1> fetching items</h1>' });

    $.ajax({
      type: 'POST',
      url: 'ajax/get_items_codes.php',
      data: {
        material_name: $('#item-search').val(),
      },
      dataType: 'JSON',
      success: function(data) {

        if(!data.success) {
          toastr.error(data.message);
          return;
        }

        $.unblockUI();  

        if($('#add-quantity').val() == "")  {
          $('#add-quantity').addClass('error-radcheck');
          toastr.error("No Quantity selected");
          return false;
        }

        if(data.response == null){
          toastr.error("Item does not exist");
        } else {
          toastr.success("Item added");

          obj.itemName = $('#item-search').val();
          obj.itemQuantity = $('#add-quantity').val();
          obj.itemCode = data.response;
          obj.maxItemQuantity = parseInt($('#add-quantity').attr("max"));

          if($('#serial_number_select').is(':visible')) {
            obj.itemSerial = $('#serial_number_select').val();
          } else if($('#add-tm-serial').is(':visible')) {
            obj.itemSerial = $('#add-tm-serial').val();                  
          }

          // if($('#add-tm-serial').val() != "") {
          //   tmSerial = $('#add-tm-serial').val();  
          // }

          customer_item_arr.push(obj);
          index = customer_item_arr.length - 1;

          if(obj.itemSerial != 'undefined' && obj.itemSerial != null) {
            $('#item-table').append("<tr data-id='"+index+"'><td><a id='remove-item'><span class='glyphicon glyphicon-trash hoverable'></span></a></td><td>"+data.response+"</td><td>"+ $('#item-search').val()+"</td><td>"+$('#add-quantity').val()+"</td><td>"+obj.itemSerial+"</td></tr>");
          } else {
            $('#item-table').append("<tr data-id='"+index+"'><td><a id='remove-item'><span class='glyphicon glyphicon-trash hoverable'></span></a></td><td>"+data.response+"</td><td>"+ $('#item-search').val()+"</td><td>"+$('#add-quantity').val()+"</td></tr>");
          }
        
          $('#item-search').val("");
          $('#add-quantity').val("");
          $('#add-quantity').prop("disabled",false);
          $('#add-tm-serial').val("");
          $('#add-tm-serial').prop("disabled",true);

        }
        
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
            toastr.error("ERROR");
            return false;
      }
    });

  });

  $('body').on('keypress','#item-search',function(){
    $("#add-quantity").attr({
       "max" : "",        
       "min" : "0"          
    });

    $.ajax({
      type: 'POST',
      url: 'ajax/get_user_stock_items.php',
      data: {
        search_val: $('#item-search').val()
      },
      dataType: 'JSON',
      success: function(data) { 
        if(!data.success) {
          toastr.error(data.message);
          return;
        }
  
        itemList = data.response;    

        $( "#item-search" ).autocomplete({
            source: function (request, response) {
               response($.map(itemList, function (value, key) {
                    return {
                        label: value.material_name,
                        value: value.material_name,
                        val: value.item_quantity
                    }
                }));
            },
            delay: 0,
            autoFocus: true,
            focus: function( event, ui ) { event.preventDefault(); },
            // minLength: 2,
            select: function(e, ui) {     
              var max = AutoCompleteSelectHandler(e, ui);
              $("#add-quantity").attr({
                 "max" : max,        
                 "min" : "0"          
              });
            }
        });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
          toastr.error("ERROR");
          return false;
      }
    });
  });

  $('body').on('focusout','#item-search',function() {
    if($(this).val() == "Thermomix TM5") {
      $('#add-quantity').val(1);
      $('#add-quantity').prop("disabled",true);
      $('#add-tm-serial').prop("disabled",false);
    } else {
      $('#add-quantity').val();
      $('#add-quantity').prop("disabled",false);
      $('#add-tm-serial').val("");
      $('#add-tm-serial').prop("disabled",true);
    }
  });

  $('body').on('click', '#remove-item', function(){

    check_session();

    var id = $(this).parent().parent().data('id');
    $(this).parent().parent().remove();

    customer_item_arr.splice(id,1);
    consultant_item_arr.splice(id,1);
  });

  $('body').on('keypress', 'input[type=text], textarea', function(){
    $(this).removeClass('error-radcheck');
  });

  $('body').on('change', 'select', function(){
    $(this).removeClass('error-radcheck');
  });


  $('body').on('click', '#add-customer', function(){

    check_session();

    var data = {};
    var valid = true;
    form = '#customerForm';

    if(customer_item_arr.length == 0){
        valid = false;
        toastr.error("cannot add order without purchase item");
        toastr.error("invalid operation");
    } else{
        valid = true;
    }

    if(checkFormValid(form) && valid) { 

      $.blockUI({ message: '<h1> Adding Customer to the system</h1>' });

      var userId = $('#user').val();
      var o = {};
      var a = $('#customerForm').serializeArray();

      $.each(a,function(){
        o[$(this)[0].name] = $(this)[0].value.replace(/["]/g,'&quot;').replace(/[\<]/g,'&lt;').replace(/[\>]/g,'&gt;') || '';
      });

      $.ajax({
        type: 'POST',
        url: 'ajax/check_user_stock.php',
        dataType: 'JSON',
        data: JSON.stringify({user_id: userId, items: customer_item_arr}),
        success: function( data ) {
          if(data.success) {
            $.ajax({
                type: 'POST',
                url: 'ajax/insertupdate_customer_details.php',
                dataType: 'json',
                data: JSON.stringify(o),
                success: function( dataCustomer ) {
                  if(!dataCustomer.success) {
                    toastr.error(dataCustomer.message);
                    return;
                  }

                  data.customer_id = dataCustomer.response;
                  o["customer_id"] = data.customer_id;

                  $.ajax({
                    type: 'POST',
                    url: 'ajax/remove_tm_invoice_number.php',
                    dataType: 'JSON',
                    data: JSON.stringify(o),
                    success: function(dataInvoiceNumber) {
                        if(!dataInvoiceNumber.success) {
                          toastr.error(dataInvoiceNumber.message);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                      console.log("INVOICE REMOVAL SERVER ERROR");
                      return false;
                    }
                  });

                  $.ajax({
                    type: 'POST',
                    url: 'ajax/insertupdate_customer_activity.php',
                    dataType: 'json',
                    data: JSON.stringify(o),
                    success: function( dataActivity ) {

                      if(!dataActivity.success){
                        toastr.error(dataActivity.message);
                        return;
                      } 

                      data.customer_activity_id = dataActivity.response;
                      $.unblockUI();
                      toastr.success("Customer added!");

                      // ADD PAYMENT NOTIFICATION
                      if($('#payment-method').val() == "") {
                        var o = {};
                        o.description = "Add Payment Method To: " + $('select[name="customer_title"]').val() + " " + $('input[name="customer_first_name"]').val() + " " + $('input[name="customer_last_name"]').val();
                        o.action = 1;
                        o.customer_activity_id = data.customer_activity_id;
                        o.customer_id = data.customer_id;
                        addUserNotifications(o); 
                      }

                      // ADD PURCHASE NOTIFICATION
                      if($('#user_type').val() == 3) {
                        var o = {};
                        o.description = "Sale by: " + $('#user_name').val();
                        o.action = 3;
                        o.customer_activity_id = data.customer_activity_id;
                        o.customer_id = data.customer_id;
                        addUserNotifications(o); 
                      }
                      $.fancybox.close();

                      $.each(customer_item_arr,function(){

                        data.material_name = $(this)[0].itemName;
                        data.material_code = $(this)[0].itemCode;
                        data.material_quantity = $(this)[0].itemQuantity;
                        data.serial_number = $(this)[0].itemSerial;

                        deductConsultantStock(userId,data.material_code,data.material_quantity); 

                        if($('#user_type').val() != 1 && data.material_code == "1000") {
                          //NOT ADMIN 
                          // ADD SERIAL NUMBER NOTIFICATION
                          var o = {}; 
                          o.description = "Add Serial Number To: " + $('select[name="customer_title"]').val() + " " + $('input[name="customer_first_name"]').val() + " " + $('input[name="customer_last_name"]').val();
                          o.action = 2;
                          o.customer_activity_id = data.customer_activity_id;
                          o.customer_id = data.customer_id;
                          addUserNotifications(o); 
                        }

                        $.ajax({
                              type: 'POST',
                              url: 'ajax/deduct_stock.php',
                              data: {
                                 code: data.material_code,
                                 quantity: data.material_quantity,                       
                              },
                              dataType: 'JSON',
                              success: function(data) {
                                if(!data.success){
                                  toastr.error(data.message);
                                }
                              },
                              error: function(XMLHttpRequest, textStatus, errorThrown) { 
                                toastr.error("ERROR");
                                return false;
                              }
                        });

                        $.ajax({
                            type: 'post',
                            url: 'ajax/insertupdate_customer_purchase.php',
                            data: data,
                            dataType: 'JSON',
                            success: function(dataPurchase) {

                                if(!dataPurchase.success) {
                                  toastr.error(dataPurchase.message);
                                  return;
                                }
                               customer_item_arr = [];

                                bootbox.confirm("click OK to generate an invoice", function(result){
                                  if(result){
                                    console.log(data);
                                    generateCustomerInvoice(data,"reload");
                                  } else {
                                    window.location.reload();
                                  }
                              });
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                              console.log("ERROR");
                              return false;
                            }
                        });
                      });
                    }
                  });
                }
              });  
              } else {
                $.unblockUI();
                toastr.error(data.message);
                return false;
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
              console.log("error with invoice");
              return false;
            }
        });
    }

  });

 
  $('body').on('click', '#add-purchase',function(){

    check_session();

    var data = {};
    var success = false;
    var valid = true;
    var customerId = $(this).data('id');
    var form = '#customerForm';

    if(customer_item_arr.length == 0){
        valid = false;
      
        toastr.error("cannot add order without purchase item");
        toastr.error("invalid operation");

    } else{
        valid = true;
    }
   
    if(checkFormValid(form) && valid) {

          $.blockUI({ message: '<h1> Adding Customer purchase</h1>' });

          var userId = $('#user').val();
          var o = {};
          var a = $('#customerForm').serializeArray();

          $.each(a,function(){
            o[$(this)[0].name] = $(this)[0].value;
          });

          o['customer_id'] = customerId;


          $.ajax({
              type: 'POST',
              url: 'ajax/check_user_stock.php',
              dataType: 'JSON',
              data: JSON.stringify({user_id: userId, items: customer_item_arr}),
              success: function( data ) {
                if(data.success) {
                  $.ajax({
                      type: 'POST',
                      url: 'ajax/remove_tm_invoice_number.php',
                      dataType: 'JSON',
                      data: JSON.stringify(o),
                      success: function(dataInvoiceNumber) {
                          if(!dataInvoiceNumber.success) {
                            toastr.error(dataInvoiceNumber.message);
                          }
                      },
                      error: function(XMLHttpRequest, textStatus, errorThrown) { 
                        console.log("INVOICE REMOVAL SERVER ERROR");
                        return false;
                      }
                    });
                    
                    $.ajax({
                          type: 'POST',
                          url: 'ajax/insertupdate_customer_activity.php',
                          dataType: 'JSON',
                          data: JSON.stringify(o),
                          success: function( dataActivity ) {

                            if(!dataActivity.success){
                              toastr.error(dataActivity.message);
                              return;
                            } 

                            $.unblockUI();

                            data.customer_activity_id = dataActivity.response;
                            data.customer_id = customerId;

                            // ADD PAYMENT NOTIFICATION
                            if($('#payment-method').val() == "") {
                              var o = {};
                              o.description = "Add Payment Method To: " + $('input[name="customer_name"]').val();
                              o.action = 1;
                              o.customer_activity_id = data.customer_activity_id;
                              o.customer_id = data.customer_id;
                              addUserNotifications(o); 
                            }

                            // ADD PURCHASE NOTIFICATION
                            if($('#user_type').val() == 3) {
                              var o = {};
                              o.description = "Sale by: " + $('#user_name').val();
                              o.action = 3;
                              o.customer_activity_id = data.customer_activity_id;
                              o.customer_id = data.customer_id;
                              addUserNotifications(o); 
                            }

                            $.each(customer_item_arr,function(){
                              data.material_name = $(this)[0].itemName;
                              data.material_quantity = $(this)[0].itemQuantity;
                              data.material_code = $(this)[0].itemCode;
                              data.serial_number = $(this)[0].itemSerial;

                              deductConsultantStock(userId,data.material_code,data.material_quantity);

                              if($('#user_type').val() != 1 && data.material_code == "1000") {
                                //NOT ADMIN 
                                // ADD SERIAL NUMBER NOTIFICATION
                                var o = {}; 
                                o.description = "Add Serial Number To: " + $('input[name="customer_name"]').val();
                                o.action = 2;
                                o.customer_activity_id = data.customer_activity_id;
                                o.customer_id = data.customer_id;
                                addUserNotifications(o); 
                              }

                              $.ajax({
                                  type: 'POST',
                                  url: 'ajax/deduct_stock.php',
                                  data: {
                                     code: data.material_code,
                                     quantity: data.material_quantity,                       
                                  },
                                  dataType: 'JSON',
                                  success: function(dataStock) {
                                    if(!dataStock.success){
                                      toastr.error(dataStock.message);
                                    }
                                  },
                                  error: function(XMLHttpRequest, textStatus, errorThrown) { 
                                    toastr.error("ERROR");
                                    return false;
                                  }
                              });

                              $.ajax({
                                  type: 'POST',
                                  url: 'ajax/insertupdate_customer_purchase.php',
                                  data: data,
                                  dataType: 'JSON',
                                  success: function(dataPurchase) {
                                      
                                      customer_item_arr = [];

                                      if(!dataPurchase.success) {

                                        toastr.error(dataPurchase.message);
                                        return;
                                      }

                                      // success = true;

                                      bootbox.confirm("click OK to generate an invoice", function(result){
                                        if(result){
                                          console.log(data);
                                          generateCustomerInvoice(data,"reload");
                                        } else {
                                          window.location.reload();
                                        }
                                      });

                                      $.fancybox.close();
                                      $.unblockUI();
                                      if(!success) {
                                        toastr.success("Customer purchase has been added");
                                      }
                                    
                                  },
                                  error: function(XMLHttpRequest, textStatus, errorThrown) { 
                                    console.log("ERROR");
                                    return false;
                                  }
                                });
                            });
                          },
                          error: function(XMLHttpRequest, textStatus, errorThrown) { 
                            console.log("ERROR");
                            return false;
                          }
                    });
                } else {
                  $.unblockUI();
                  toastr.error(data.message);
                  return false;
                }
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) { 
                console.log("SERVER ERROR");
                return false;
              }
          });
      }
  });


  //INVENTORY

  $('body').on('click','#edit-stock',function(){

    check_session();
    
    $("input[type='number'][name='stock-levels']").each(function(){ 
      $(this).prop('readonly',false);
    });

    $('#save-stock').prop('disabled',false);
    $('#edit-stock').prop('disabled',true);
  });

  var stockLevels = new Array();

  $('body').on('click','#save-stock',function(){

    check_session();

    bootbox.confirm("Click 'OK' to confirm",function(result){
      if(result){
        $.blockUI({ message: '<h1>Updating inventory</h1>' });
          $("input[type='number'][name='stock-levels']").each(function(){ 
            stockLevels.push({
              itemCode: $(this).data('code'),
              quantity: $(this).val()
            });
          });
                              
          $.ajax({
                type: 'POST',
                url: 'ajax/update_stock_levels.php',
                dataType: 'text',
                data: JSON.stringify(stockLevels),
                dataType: 'JSON',
                success: function( data ) {

                  if(!data.success) {

                    toastr.error(data.message);
                    return;
                  }

                  $.unblockUI();
                  window.location.href = 'index.php?view=inventory'; //check this!!!!!!!
                  toastr.success("stock altered");
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                  console.log("ERROR");
                  return false;
                }
                 
          });
      }

    });
  });


  $('body').on('keypress','#material-search',function(){

    check_session();
     
    $('#material-search').siblings('span.input-group-btn').find('i.fa-search').removeClass('fa-search').addClass('fa-refresh fa-spin');

    $.ajax({
          type: 'POST',
          url: 'ajax/item_search.php',
          data: {
            search_val: $('#material-search').val()
          },
          dataType: 'JSON',
          success: function( data ) {  

            if(!data.success) {

              toastr.error(data.message);
              return;
            }

            $('#material-search').siblings('span.input-group-btn').find('i.fa-refresh.fa-spin').removeClass('fa-refresh fa-spin').addClass('fa-search');

            itemList = data.response.material_name;
            
          $( "#material-search" ).autocomplete({
                source: itemList,
                delay: 0,
                autoFocus: true,
                focus: function( event, ui ) { event.preventDefault(); },
                select: function(e, ui) {     
                var indexOfItem = data.response.material_name.indexOf(ui.item.value);

                $("input[type='number'][name='stock-levels']").each(function(){ 
                  $(this).parent().parent().removeClass('success');
                if($(this).data('code') == data.response.material_code[indexOfItem]){
                  $(this).parent().parent().addClass('success');
                  $('html, body').animate({
                                scrollTop: $(this).offset().top - 200
                        }, 2000);
                }
            });

          }
          });
          }
    });
  });

  //NOTIFICATIONS

  $('body').on('click','.getperson, a.getPerson',function(e){
    data = {};
    e.preventDefault()
    e.stopImmediatePropagation();

    var action = $(this).data("action");
    var customerId = $(this).data("customer");
    var activityId = $(this).data("id");
    var userId = $(this).data("user-id");
    var notifId = $(this).data("notification-id");

    if($(this).data("cons-sale")) {
      $.fancybox.open({
          padding: 0,
          minWidth:'10%',
          minHeight:'10%',
          type: 'ajax',
          href: 'views/activity_details.php',
          modal: false,
          type: "ajax",
          ajax: {
              type: "POST",
              data: JSON.stringify(activityId),
          },
          helpers: { 
            overlay: { 
                locked: false 
            }
          },
          beforeClose : function () {
            removeUserNotification(notifId);
          } 
        });
    } else {
      getPerson(customerId,action,activityId,userId,notifId);
    }
   
  });

  $('body').on('click','#reset-stock',function(){

    check_session();

    $.blockUI({ message: '<h1>Loading inventory</h1>' }); //check if this works 
    window.location.href = 'index.php?view=inventory';
    $.unblockUI();
  });

  $('body').on('click','#add-details',function(){
      var obj = {};
      var objLinks = [];
      var data = {};
      data.id = $(this).data("id");
      data.payment_method  = $('select[name="payment_method"]').val();

      var uid = $(this).data("user-id");
      var notifId = $(this).data("notif-id");

      // var data = $('#customerForm').serializeArray();
       $.ajax({
        type: 'POST',
        url: 'ajax/insertupdate_customer_activity.php',
        dataType: 'JSON',
        data: JSON.stringify(data),
        success: function( data ) {
          if(data.success){
            removeUserNotification(notifId);
            toastr.success("Details added!"); 
            $.fancybox.close();

            obj.href="timeline";
            objLinks.push("&type=muser");
            obj.objLinks = objLinks;
            hyperlinkObj(obj);
          } else {
            toastr.error(data.message);
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("error with invoice");
          return false;
        }
      });
  });

  //REPORTS

  $('body').on('click','.view-sales',function(){
    var data = {};
    var type = $(this).data("type");
    data.type = type;

    $.ajax({
        type: 'POST',
        url: 'ajax/view_report.php',
        dataType: 'JSON',
        data: JSON.stringify(data),
        success: function( data ) {
         if(data.success) {
          createSalesChart(data.response);
         } else {
          toastr.error(data.message);
         }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("SERVER REQUEST ERROR");
          return false;
        }
    });
  });

  $('body').on('click', 'a', function(e){
    if (!$(this).hasClass('ignore')){
      e.preventDefault();
      e.stopImmediatePropagation();
      hyperlink($(this));
    }
  });

  $(window).on('click', 'a', function(e){
    if (!$(this).hasClass('ignore')){
      e.preventDefault();
      e.stopImmediatePropagation();
      hyperlink($(this));
    }
  });

  $(document).on('click', 'a', function(e){
    if (!$(this).hasClass('ignore')){
      e.preventDefault();
      e.stopImmediatePropagation();
      hyperlink($(this));
    }
  });

  // $('body').fancybox({
  //   onClosed: function() {
  //     customer_item_arr = [];
  //     consultant_item_arr = [];
  //   })
  // });
  
  // $(window).fancybox({
  //   onClosed: function() {
  //     customer_item_arr = [];
  //     consultant_item_arr = [];
  //   })
  // });
  
  // $(document).fancybox({
  //   onClosed: function() {
  //     customer_item_arr = [];
  //     consultant_item_arr = [];
  //   })0
  // });

  


});

  //FUNCTIONS

  function createSalesChart(data) {
    if(data.length == 0) {
      toastr.error("You Have No Sales");
      return false;
    }

    var salesType;
    var salesTitle;

    var seriesAll = [];
    var categoriesArr = [];

    var seriesArr = [];
    var seriesObj = {};

    $.each(data,function() {
      seriesObj = {};

      switch($(this)[0].flag) {
        case 'all': 
          salesType = $(this)[0].flag;
          salesTitle = 'General Sales';
          categoriesArr.push($(this)[0].item_description);
          seriesArr.push(parseInt($(this)[0].item_quantity));
          break;
        case 'tm5_sales':
          salesType = $(this)[0].flag;
          salesTitle = 'TM5 Sales';
          categoriesArr.push($(this)[0].username);
          seriesArr.push(parseInt($(this)[0].item_quantity));
          break;

        case 'books_parts_sales':
          salesType = $(this)[0].flag
          salesTitle = 'Books and Parts Sales';
          categoriesArr.push($(this)[0].item_description);

          seriesArr = [];

          if(checkPresentObject($(this)[0].username,seriesAll) == true) {
            // name already present
          } else {
            seriesObj.name = $(this)[0].username;

            $.each(data,function() {
              if($(this)[0].username == seriesObj.name) {
                seriesArr.push(parseInt($(this)[0].item_quantity));
              } else {
                seriesArr.push(null);
              }
            });
            seriesObj.data = seriesArr;
            seriesAll.push(seriesObj);
          }
          break;
      }



      // if(salesType == "books_parts_sales") {
      //   $.each(categoriesArr,function(){
      //     seriesArr = [];
      //     seriesObj = {};

      //     $.each(data,function() {

      //     });

      //   });
      // }

    });

    var chart = Highcharts.chart('Reports-charts', {
      colors: ['#3CB371', '#2F4F4F', '#96CDCD', '#F0E68C', '#78AB46'],
      chart: {
        type: 'bar'
      },
      title: {
          text: salesTitle
      },
      xAxis: {
          categories: categoriesArr,
          title: {
              text: null
          } 
      },
      yAxis: {
        min: 0,
        title: {
            text: 'Sales',
            align: 'high'
        },
        labels: {
          enabled: false
          // overflow: 'justify'
        }
      },
      tooltip: {
        valueSuffix: ' sales'
      },
      plotOptions: {
        bar: {
            dataLabels: {
              enabled: true
            }
        }
      },
      // legend: {
      //   layout: 'vertical',
      //   align: 'right',
      //   verticalAlign: 'top',
      //   x: -40,
      //   y: 80,
      //   floating: true,
      //   borderWidth: 1,
      //   backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
      //   shadow: true
      // },
      credits: {
        enabled: false
      },
      series: []
    });

    if(salesType == 'books_parts_sales') {
      $.each(seriesAll,function() {
        chart.addSeries({ 
          name: $(this)[0].name,
          data: $(this)[0].data
        }, false);
        chart.redraw();
      });
    } else {
      chart.addSeries({     
        data: seriesArr
      }, false);
      chart.redraw();
    }
  
  }

  function checkPresentObject(sVal,ObjArray) {
    var ret;
    $.each(ObjArray,function(){
      if($(this)[0].name == sVal) {
        ret= true;
      }
    });
    return ret;
  }

  function hyperlink(element){
    check_session();

    var id_red = '';
    var link_href = element.attr('href');
    if(element.data("id")) {
      var id_red = "&id=" + element.data("id");
    } 
    // if (!element.hasClass('ignore')){
      // e.preventDefault();
      // e.stopImmediatePropagation();
      if (typeof link_href !== typeof undefined && link_href != "" && link_href != "#") {
        $.blockUI({ message: '<h3>Loading</h3>' });
        var url = location.protocol+'//'+location.host+location.pathname+'?view=' + link_href + id_red;
        var url_k = CryptoJS.SHA512(url);
        window.location.href = url+'&url_k='+url_k;
      }
    // } else {

    // }
  }

  function hyperlinkObj(object){
    check_session();

    var obj_links = '';
    var link_href = object.href;

    console.log(object.objLinks);
    if(object.objLinks) {
      $.each(object.objLinks,function(index, value){
        obj_links += value;
      });
    }

    if (typeof link_href !== typeof undefined && link_href != "" && link_href != "#") {
      $.blockUI({ message: '<h3>Loading</h3>' });
      var url = location.protocol+'//'+location.host+location.pathname+'?view=' + link_href + obj_links;
      var url_k = CryptoJS.SHA512(url);
      window.location.href = url+'&url_k='+url_k;
    }

  }

 function check_session() {

    $.ajax({
      url: 'ajax/keep_alive.php',
      type: 'get',
      dataType: 'json', 
      cache: false
    }).done(function (data) {
      if (!data.success) {
        bootbox.alert(data.message,function() {
          if (data.refresh) {
            $.unblockUI();
            window.location.reload();
          }
          return false;
        });
      }
    }).fail(function (jqXHR) {
      console.log( "check session error: "+jqXHR.responseText); 
    });

  }

  function myKeyPress(e) {
    var keynum;

      if(window.event) { // IE                    
        keynum = e.keyCode;
      } else if(e.which){ // Netscape/Firefox/Opera                   
        keynum = e.which;
      }
      return String.fromCharCode(keynum);
  }


  function getTimeline(type)  { 
    
    check_session();

    if(type == 'undefined') {
      type = null;
    }

    $.blockUI({ message: '<h1>Loading timeline</h1>' });

    $('#timeline-content').empty();

    $.ajax({
        type: 'POST',
        url: 'ajax/get_timeline.php',
        dataType: 'JSON',
        data: {"type" : type},
        success: function(data) {

          if(!data.success) {
            if(data.message == "404") {
              $.unblockUI();
              bootbox.confirm("Select a customer or consultant to view their timeline.",function(result){
                if(result){
                   window.location.href = 'index.php?view=welcome_screen'; 
                }
              });
              return;
            }
            toastr.error(data.message);
          }

          $.unblockUI();
          $('.timeline').append(data.response);

          if(data.message == "cust") {
            $('.cmn-toggle').parent().parent().removeClass('col-xs-3');
            $('.cmn-toggle').parent().parent().addClass('col-xs-4');
            $('#stats-view-container').addClass("hidden");
          } else {
             if($('#stats-view-container').hasClass("hidden")){
              $('#stats-view-container').removeClass('hidden');
              $('#timeline-view-container').addClass('hidden');
            }
            if($('#stats-view-container').hasClass('hidden')) {
              $('#stats-view-container').removeClass('hidden');
              $('#timeline-view-container').addClass('hidden');
            }
            // $('#consultant-heading').html($('#consultant_title').val());
            // if($('#cons-stock-panel').is(':visible')) {
            //   $('#cons-stock-panel').addClass('hidden');
            // }
          }

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("ERROR");
          return false;
        }
    });
  }

  function generateInvoice(customerId, activityId, invoiceNumber, activityDate, serialNumber){

    check_session();

    $.blockUI({ message: '<h1>Downloading</h1>' });
    $.ajax({
          type: 'POST',
          url: 'ajax/generate_excel_invoice.php',
          data: {
                activity_id: activityId,
                invoice_number: invoiceNumber,
                date: activityDate,
                serial_number: serialNumber,
                customer_id: customerId,
          },
          success: function( data ) {
            $.unblockUI();
            window.open("ajax/generate_excel_invoice.php","_blank");
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log("error with invoice");
            return false;
          }
    });
  }



 function findSparePart(searchValue) {
    var item=$('td').find('input:checkbox#'+searchValue).closest('tr');
    $('td').find('input:checkbox#'+searchValue).closest('tr').addClass('info');
    $('html, body').animate({
               scrollTop: $(item).offset().top - 200
        }, 2000);

  }

  function addConsultantStock(id, arr) {
    var data = {};
    data.user_id = id;
    data.items = arr;

    $.ajax({
          type: 'POST',
          url: 'ajax/update_user_stock.php',
          data: data,
          dataType: 'JSON',
          success: function( data ) {
            if(!data.success) {
              toastr.error(data.message);
              return;
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log("error with invoice");
            return false;
          }
    });
  }

  function addConsultantGift(id, objArr) {
    var data = {};
    data.consultant_id = id;
    data.items = objArr;

    $.ajax({
          type: 'POST',
          url: 'ajax/add_consultant_gift.php',
          data: data,
          dataType: 'JSON',
          success: function( data ) {
            if(!data.success) {
              toastr.error(data.message);
              return;
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log("error with invoice");
            return false;
          }
    });
  }

  function deductConsultantStock(id, materialCodes, materialQuantities) {

    var data = {user_id:id, code: materialCodes, quantity: materialQuantities};

    $.ajax({
          type: 'POST',
          url: 'ajax/deduct_consultant_stock.php',
          data: data,
          dataType: 'JSON',
          success: function( data ) {
            if(!data.success) {
              toastr.error(data.message);
              return;
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            return false;
          }
    });
  }


  function getConsultants() {
    
   $.ajax({
        type: 'GET',
        url: 'ajax/get_consultants.php',
        dataType: 'JSON',
        success: function( data ) {
          if(!data.success) {
            toastr.error(data.message);
            return;
          }
          return data.response;
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("error with invoice");
          return false;
        }
    });
  }

  function createConsultantStockTable(data) {

    if(table != null && table != "" && table != "undefined") {
      // bootbox.alert('test');
    }

    var table = $('#consultant_stock_table').DataTable({
      "bPaginate": false,
       destroy: true
    });


    table.clear();

    $.each(data,function(){
      table.row.add([$(this)[0].item_name,$(this)[0].item_code,$(this)[0].item_quantity]).draw(true);
    });
  }

  function formhash(form, password) {
    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");
 
    // Add the new element to our form. 
    form.append(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.val());
 
    // Make sure the plaintext password doesn't get sent. 
    password.val("");

    return true;
  }

  function regformhashPassword(form, email, password, conf) {
    if (email.val() == ''|| password.val() == '')  {
        bootbox.alert('You must provide all the requested details. Please try again');
        return false;
    }

    if (password.val().length < 6) {
        bootbox.alert('Passwords must be at least 6 characters long.  Please try again');
        password.focus();
        return false;
    }

    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
    if (!re.test(password.val())) {
        bootbox.alert('Passwords must contain at least one number, one lowercase and one uppercase letter.  Please try again');
        return false;
    }

    if (password.val() != conf.val()) {
        bootbox.alert('Your password and confirmation do not match. Please try again');
        form.password.focus();
        return false;
    }

    var p = document.createElement("input");
    form.append(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.val());
    password.val("");

    return true;
  }

  function regformhash(form, username) {
  
      re = /^\w+$/; 
      if(!re.test(username.val())) { 
          bootbox.alert("Username must contain only letters, numbers and underscores. Please try again"); 
          username.focus();
          return false; 
      }

      return true;
  }

  function getUserNotifications() {
    var notifications = '';

    $.ajax({
        type: 'POST',
        url: 'ajax/get_user_notifs.php',
        dataType: 'JSON',
        success: function( data ) {
          if(data.success){
            if(data.response.length > 0) {
              $('.badge-notify').text(data.response.length);
              $.each(data.response,function() {
                if($(this)[0].action_id == 3) { //SALE FROM CONSULTANT
                  notifications += '<a data-cons-sale="1" class="content getperson" href="#" data-customer="'+$(this)[0].customer_id+'" data-action="'+$(this)[0].action_id+'" data-id="'+$(this)[0].customer_activity_id+'" data-notification-id="'+$(this)[0].id+'" data-user-id="'+$(this)[0].uid+'">';
                } else {
                  notifications += '<a class="content getperson" href="#" data-customer="'+$(this)[0].customer_id+'" data-action="'+$(this)[0].action_id+'" data-id="'+$(this)[0].customer_activity_id+'" data-notification-id="'+$(this)[0].id+'" data-user-id="'+$(this)[0].uid+'">';
                }
                notifications += '<div class="notification-item">';
                if($(this)[0].action_id == 3) { //SALE FROM CONSULTANT
                  notifications += '<h4 data-cons-sale="1" class="item-title getperson" data-customer="'+$(this)[0].customer_id+'" data-action="'+$(this)[0].action_id+'" data-id="'+$(this)[0].customer_activity_id+'" data-notification-id="'+$(this)[0].id+'" data-user-id="'+$(this)[0].uid+'">Notification Issued '+$(this)[0].days_due+' • day(s) ago</h4>';
                } else {
                  notifications += '<h4 class="item-title getperson" data-customer="'+$(this)[0].customer_id+'" data-action="'+$(this)[0].action_id+'" data-id="'+$(this)[0].customer_activity_id+'" data-notification-id="'+$(this)[0].id+'" data-user-id="'+$(this)[0].uid+'">Notification Issued '+$(this)[0].days_due+' • day(s) ago</h4>';
                }
                notifications += '<p class="item-info">'+$(this)[0].description+'</p>';
                notifications += '</div>';
                notifications += '</a>';
              });
            } else {
                notifications += '<a class="content" href="#">';
                notifications += '<div class="notification-item">';
                notifications += '<p class="item-info">No Notifications</p>';
                notifications += '</div>';
                notifications += '</a>';
            }

            $('.notifications-wrapper').append(notifications);

          } else {
            toastr.error(data.message);
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("error getting notifications");
          return false;
        }
    });
  }

  function addUserNotifications(data) {
    $.ajax({
        type: 'POST',
        url: 'ajax/add_user_notifs.php',
        dataType: 'JSON',
        data: JSON.stringify(data),
        success: function( data ) {
          if(data.success){
            console.log("added notification");
          } else {
            toastr.error(data.message);
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("error with invoice");
          return false;
        }
    });
  }

  function getPerson(id,action,activityId,userID,notifID) {
    var personObj = {};
    var data = {};
    var url = 'views/confirm_person.php';

    data.action = action;
    data.id = id;
    data.user_id = userID;
    data.notif_id = notifID;

    if(activityId==null || activityId=="undefined") {
      var actID = '';
    } else {
      var actID = '&id='+activityId;
    }

    if(userID==null || userID=="undefined") {
      var uid = '';
    } else {
      var uid = '&uid='+userID;
    }

    if(notifID==null || notifID=="undefined") {
      var nid = '';
    } else {
      var nid = '&nid='+notifID;
    }

    if(action==null || action=="undefined") {
      action = '';
    } else {
      // data.action = action;
      url = 'views/confirm_person.php?action='+action+actID+uid+nid;
    }
    
    $.ajax({
        type: 'POST',
        url: 'ajax/get_persons.php',
        dataType: 'JSON',
        data: JSON.stringify(data),
        success: function( data ) {
          if(data.success){
            var personObj = data.response;
            
            $.fancybox.open({
              padding: 20,
              minWidth:'20%',
              height: '60%',
              minHeight:'20%',
              type: 'ajax',
              href: url,
              modal: true,
              type: "ajax",
              ajax: {
                  type: "POST",
                  data: JSON.stringify(personObj[0]),
              },
              helpers: { 
                  overlay: { 
                      locked: false 
                  } 
              }
            });
          
          } else {
            toastr.error(data.message);
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("SERVER ERROR");
          return false;
        }
    });
  }

  function execAction(action,id,uid,nid) {
    if(id==null || id == 'undefined') {
      id = null;
    }
    if(uid==null || uid == 'undefined') {
      uid = null;
    }
    if(nid==null || nid == 'undefined') {
      nid = null;
    }

    switch(action) {
      case 1:
          action1(id,uid,nid);
          break;
      case 2:
          action2(id,uid,nid);
          break;
      default:
          //code block
    } 
  }

  function action1(id,uid,nid) {
    $.unblockUI();
    data = {};
    data.id = id;
    data.action = 'add-details';
    data.user_id = uid;
    data.notification_id = nid;

    //add payment option to customer
    $.fancybox.open({
        padding: 0,
        minWidth:'20%',
        minHeight:'20%',
        type: 'ajax',
        href: 'views/add_customer.php',
        modal: false,
        type: "ajax",
        ajax: {
            type: "POST",
            data: JSON.stringify(data),
        },
        helpers: { 
          overlay: { 
              locked: false 
          } 
        },
        beforeClose : function () {
          $.get('ajax/unset_customer_session.php');
        } 
    });

  }

  function action2(id,uid,nid) {
    $.unblockUI();
    data = {};
    data.id = id;
    data.action = 'add-details';
    data.user_id = uid;
    data.notification_id = nid;
    
    //add serial number to customer
    $.fancybox.open({
        padding: 0,
        minWidth:'20%',
        minHeight:'20%',
        type: 'ajax',
        href: 'views/add_serial.php',
        modal: true,
        type: "ajax",
        ajax: {
            type: "POST",
            data: JSON.stringify(data),
        },
        helpers: { 
          overlay: { 
              locked: false 
          } 
        },
        beforeClose : function () {
          $.get('ajax/unset_customer_session.php');
        } 
    });
  }

  function removeUserNotification(id) {
    var data = {};
    data.id = id;

    $.ajax({
        type: 'POST',
        url: 'ajax/remove_user_notification.php',
        dataType: 'JSON',
        data: JSON.stringify(data),
        success: function( data ) {
          if(data.success){
            console.log("removed notification");
          } else {
            toastr.error(data.message);
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("error with invoice");
          return false;
        }
    });
  }


  function generateCustomerInvoice(data,flag) {
    $.blockUI({ message: '<h1>Downloading</h1>' });
    var obj = encodeURIComponent(JSON.stringify(data));

     $.ajax({
        type: 'POST',
        url: 'ajax/generate_excel_invoice.php',
        data: {"oa":1},
        success: function( data ) {
          $.unblockUI();
          window.open('ajax/generate_excel_invoice.php?data='+obj,'_blank' );
          if(flag == "reload") {
            window.location.reload();
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          console.log("error with invoice");
          return false;
        }
    });
  }

  function AutoCompleteSelectHandler(event, ui) {               
    var selectedObj = ui.item;              
    return selectedObj.val;
  }

  // function AutoCompleteSelectHandlerGetPersons(event,ui) {
  //   var selectedObj = ui.item; 
  //   return selectedObj.value;
  // }

  function addTMPurchase() {

    var obj = {}
    obj.itemName = "Thermomix TM5";
    obj.itemQuantity = 1;
    obj.itemCode = "1000";

    return obj;
  }


 function checkFormValid(form){ 
    var scrollTo = true;
    var valid = true;
    $(form+' :input.mandatory-field').each(function(){
      if($(this).val() == "" && $(this).is(':visible')) {
        $(this).addClass('error-radcheck');        
         if(scrollTo) {
            $(this).closest('.fancybox-inner').animate({
              scrollTop: $(this).offset().top - 130
            },'fast');
            scrollTo = false;  
          }                          
        valid = false;
      } else {
        $(this).removeClass('error-radcheck');
      }
    });
    var radio_groups = {}
    $(form+" :radio").each(function(){
        if($(this).hasClass('mandatory-field') && $(this).is(':visible')) {
          radio_groups[this.name] = true;
        }
    });
    for(group in radio_groups) {
        if_checked = !!$(":radio[name='"+group+"']:checked").length
        if(if_checked) { 
          $("input[name='"+group+"']").parent().removeClass('error-radcheck');
          if(scrollTo) {
            $(this).closest('.fancybox-inner').animate({
              scrollTop: $("input[name='"+group+"']").offset().top - 130
            },'fast');
            scrollTo = false;  
          }   
        } else {
          $("input[name='"+group+"']").parent().addClass('error-radcheck');
          valid = false; 
      }
    }
    // var oneChecked = false;
    // $('input:checkbox').each(function(){
    //     if($(this).hasClass('mandatory-field') && $(this).is(':checked')) {
    //       oneChecked = true;
    //     }
    // });
    // if(!oneChecked) {
    //   $('input:checkbox').parent().parent().parent().addClass('checkbox-validate');
    //   valid = false;
    // } else {
    //   $('input:checkbox').parent().parent().parent().removeClass('checkbox-validate');
    // }

    return valid;
  }