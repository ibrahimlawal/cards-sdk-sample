// You need not edit this file
// It's fully optimized to work with
// pay.html to conclude payment

function startPaystack(access_code){
    showCardEntry();

    function chargeCard(){
        $.ajax({
            type: "POST",
            url: 'charge-card',
            data: {
                pan: $("#number").val(),
                cvc: $("#cvv").val(),
                exp_month: $("#expiryMonth").val(),
                exp_year: $("#expiryYear").val(),
                device: 'getdevice',
                access_code: access_code,
                pin: fetchValueWhileClearingField('pin')
            },
            success: function (r) {
                if(!(typeof r.err === 'undefined')){
                    handleError(r.err);
                }
                handleResponse(r);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                e={message:xhr.responseText};
                handleError(e);
            }
        });
    }

    function validatePorT(PorT, type){
        $.ajax({
            type: "POST",
            url: 'validate',
            data: {
                access_code: access_code,
                type: type,
                token: PorT
            },
            success: function (r) {
                if(!(typeof r.err === 'undefined')){
                    handleError(r.err);
                }
                handleResponse(r);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                e={message:xhr.responseText};
                handleError(e);
            }
        });
    }

    function showCardEntry(){
        stopProcessing();
        $("#card-form").show();
        $("#failed").hide();
        $("#number").val('');
        $("#cvv").val('');
        $("#expiryMonth").val('');
        $("#expiryYear").val('');
        $("#card-form").submit(function(evt){
            startProcessing(evt);
            chargeCard();
        });
    }

    function startProcessing(e){
        e.preventDefault();
        $("#processing").show();
        e.target && $(e.target).hide();
        e.target && $(e.target).off('submit');
        $("#error").hide();
        $("#error-message").html('');
        $("#error-errors").html('');
    }

    function stopProcessing(){
        $("#processing").hide();
    }

    function startPinAuth(response){
        $("#pin-form").show();
        $("#pin-form").submit(function(e){
            startProcessing(e);
            chargeCard();
        });
    }

    function startOtpAuth(response){
        $("#otp-form").show();
        $("#otp-message").html(response.message);
        $("#otp-form").submit(function(e){
            startProcessing(e);
            validatePorT(fetchValueWhileClearingField('otp'), 'otp');
        });
    }

    function start3dsAuth(response){
        $("#3ds-form").show();
        $("#3ds-message").html(response.message);
        $("#3ds-form").submit(function(e){
            startProcessing(e);
            window.open(response.url, "3ds-iframe");;
        });
    }

    function startPhoneAuth(response){
        $("#phone-form").show();
        $("#phone-message").html(response.message);
        $("#phone-form").submit(function(e){
            startProcessing(e);
            validatePorT(fetchValueWhileClearingField('phone'), 'phone');
        });
    }

    function showTimeout(response){
        $("#timeout").show();
        $("#timeout-message").html(response.message);
    }

    function showSuccess(response){
        $("#success").show();
        $("#success-message").html(response.message);
        $("#success-reference").html(response.data.reference);
        verifyTransactionOnBackend(response.data.reference);
    }

    function showFailed(response){
        $("#failed").show();
        $("#failed-message").html(response.message);
        showCardEntry();
    }

    function handleResponse(response){
        console.log(response);
        stopProcessing();
        switch(response.status) {
            case 'authpin':
                startPinAuth(response);
                break;
            case 'authphone':
                startPhoneAuth(response);
                break;
            case 'authotp':
                startOtpAuth(response);
                break;
            case 'auth3DS':
                start3dsAuth(response);
                break;
            case 'timeout':
                showTimeout(response);
                break;
            case 'success':
                showSuccess(response);
                break;
            case 'failed':
                showFailed(response);
                break;
        }
    }

    function handleError(error){
        $("#error").show();
        showPaystackError(error);
        console.log(error);
        reportErrorToBackend(error);
        showCardEntry();
    }

    function fetchValueWhileClearingField(id){
        var val = $('#'+id).val();
        $('#'+id).val('');
        return val;
    }

    function showPaystackError(error){
        if(!(typeof error.message === 'string')){
            // Not a paystack error
            return;
        }
        $("#error-message").html(error.message);
        if(!(Object.prototype.toString.call( error.errors ) === '[object Array]')){
            // Not an array of messages
            return;
        }
        var len = error.errors.length;
        // build the error string
        var errStr = '<ul>';
        for (i=0; i<len; ++i) {
            errStr = errStr+'<li>'+error.errors[i].field+': '+error.errors[i].message+'</li>';
        }
        $("#error-errors").html(errStr+'</ul>');
    }

}

