$.ajaxSetup({
        beforeSend: function(xhr, type) {
            if (!type.crossDomain) {
                xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
            }
        },
    });

/*  -----------> For Hard Delete data from table. <----------- */

  function hardDelete(id)
  {
    var table = $('#table_name').val();
    var data_table = $('#data_table_name').val();

    swal({
        title: "Are you sure?",
        text: "You want to permanently delete this record !!",
        icon: 'warning',
        buttons: [
          "No, cancel it!",
          "Yes, I am sure!"
        ],
        dangerMode: true,
      }).then(function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: SITEURL + "/admin/hard-delete",
            data:{id:id,table:table,_token: $('meta[name="csrf-token"]').attr('content') },
            method:'post',
            datatype: 'json',             
            success:function(res){
              if(res == 1){
                swal({
                  title: "Success!",
                  text: "record removed successfully.",
                  icon: "success",
                  button: "Ok"
                });
                var oTable = $('#' +data_table).dataTable(); 
                oTable.fnDraw(false);
              }
            }
          });
        
        } else {
          swal({
            title: "Cancelled",
            text: "Your imaginary file is safe :)",
            icon: 'error',
            button: "Ok"
          });
          // swal({
          //   lang.canceled, lang.your_imaginary_file_is_safe, "error");
          var oTable = $('#' +data_table).dataTable(); 
          oTable.fnDraw(false);
        }
      });
  }
/*  ===============> End <===============   */

/*  -----------> For Hard Delete data from table. <----------- */

  function questionDelete(id)
  {
    var table = $('#table_name').val();
    var data_table = $('#data_table_name').val();

    swal({
        title: "Are you sure?",
        text: "You want to permanently delete this record !!",
        icon: 'warning',
        buttons: [
          "No, cancel it!",
          "Yes, I am sure!"
        ],
        dangerMode: true,
      }).then(function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: SITEURL + "/admin/question-delete",
            data:{id:id,table:table,_token: $('meta[name="csrf-token"]').attr('content') },
            method:'post',
            datatype: 'json',             
            success:function(res){
              if(res == 1){
                swal({
                  title: "Success!",
                  text: "record removed successfully.",
                  icon: "success",
                  button: "Ok"
                });
                var oTable = $('#' +data_table).dataTable(); 
                oTable.fnDraw(false);
              }
            }
          });
        
        } else {
          swal({
            title: "Cancelled",
            text: "Your imaginary file is safe :)",
            icon: 'error',
            button: "Ok"
          });
          // swal({
          //   lang.canceled, lang.your_imaginary_file_is_safe, "error");
          var oTable = $('#' +data_table).dataTable(); 
          oTable.fnDraw(false);
        }
      });
  }
/*  ===============> End <===============   */

/*  -----------> For Read Image Url. <----------- */
  function readURL(input, id = '#old_path') {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(id)
            .attr('src', e.target.result)
            .width(150)
            .height(150);
        };
        reader.readAsDataURL(input.files[0]);
    }
  }
/*  ===============> End <===============   */