var GblastForm = function () {

    return {
        
        //Gblast
        initGblastForm: function () {
	        
	        // Validation
	        $("#sky-form1").validate({
	            // Rules for form validation
	            rules:
	            {

	                email:
	                {
	                    required: true,
	                    email: true
	                },
	                percent:
	                {
	                    required: true
	                },
	                evalue:
	                {
	                    required: true
	                }
	            },
	                                
	            // Messages for form validation
	            messages:
	            {

	                email:
	                {
	                    required: 'Please enter your email address',
	                    email: 'Please enter a VALID email address'
	                },
	                percent:
	                {
	                    required: 'Please select Percent Identity'
	                },
	                evalue:
	                {
	                    required: 'Please select your E-value'
	                }
	            },

	            // Ajax form submition
	            submitHandler: function(form)
	            {
	                $(form).ajaxSubmit(
	                {
	                    beforeSend: function()
	                    {
	                        $('#sky-form1 button[type="submit"]').addClass('button-uploading').attr('disabled', true);
	                    },
	                uploadProgress: function(event, position, total, percentComplete)
	                {
	                    $("#sky-form1 .progress").text(percentComplete + '%');
	                },
	                    success: function()
	                    {
	                        $("#sky-form1").addClass('submited');
	                        $('#sky-form1 button[type="submit"]').removeClass('button-uploading').attr('disabled', false);
	                    }
	                });
	            },  
	            
	            // Do not change code below
	            errorPlacement: function(error, element)
	            {
	                error.insertAfter(element.parent());
	            }
	        });
        }

    };

}();