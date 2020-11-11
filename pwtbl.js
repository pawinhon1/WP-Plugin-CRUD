jQuery(document).ready(function($){
  jQuery('#create-tbl').hide();

  jQuery('#btn-create-tbl').click(function(){
    jQuery('#create-tbl').show();
  });

  var id = 1;
  jQuery("#btnn-1").click(function(){
    var newid = id++;
    jQuery("#pwtbl-table").append('<tr><td><input type="text" name="flname'+newid+'" id="flname"></td>\n\
    <td>\n\
      <select name="fltype'+newid+'">\n\
        <option value="INT(200)">int(200)</option>\n\
        <option value="VARCHAR(200)">varchar(200)</option>\n\
      </select>\n\
    </td>\n\
    <td>\n\
      <select name="flnull'+newid+'">\n\
        <option value="">Default</option>\n\
        <option value="NOT NULL">Not Null</option>\n\
      </select>\n\
    </td></tr>');
  });


});

