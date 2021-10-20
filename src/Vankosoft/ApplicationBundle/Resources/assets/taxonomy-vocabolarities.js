$( function()
{
	jQuery("#btnDelete").on("click", function (e) {
	    var link = this;
	    e.preventDefault();

	    jQuery("<div>Do you want to delete this Item?</div>").dialog({
	        buttons: {
	            "Ok": function () {
	                window.location = link.href;
	            },
	            "Cancel": function () {
	                jQuery(this).dialog("close");
	            }
	        }
	    });
	});
});
