(function ($) {

    'use strict';


    function number_format (number, decimals, decPoint, thousandsSep) {

        number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
        var n = !isFinite(+number) ? 0 : +number
        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
        var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
        var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
        var s = ''

        var toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec)
            return '' + (Math.round(n * k) / k)
                .toFixed(prec)
        }

        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || ''
            s[1] += new Array(prec - s[1].length + 1).join('0')
        }

        return s.join(dec)
    }


    //Main output Function
    function mcOutputFunc(){

        // Getting output div id
        var outputDiv = $("#mc-output");

        // Getting total amount value from user
        var mcTotalAmount = parseFloat( $("#mc-total-amount").val() );

        //Getting down payment value from user
        var mcDownPayment = parseFloat( $("#mc-down-payment").val() );

        //Getting interest rate value from user
        var mcInterestRate = parseFloat( $("#mc-interest-rate").val() );

        //Getting mortgage period value from user
        var mcAmortizationPeriod = parseFloat( $("#mc-mortgage-period").val() );

        //Calculating principal amount by subtracting down payment from total amount
        var principal = mcTotalAmount - mcDownPayment;

        if ( 0 !== mcInterestRate ){

            //Calculating r by this formula ( (InterestRate/100)/12 )
            var r = ((mcInterestRate / 100) / 12);

            // Power calculating by this formula Math.pow(base, exponent)
            var power = Math.pow((1 + r), (mcAmortizationPeriod * 12));

            // Calculating total mortgage
            var monthlyMortgage = principal * ((r * power) / (power - 1));

        } else {

            var monthlyMortgage = principal / ( mcAmortizationPeriod * 12 );
        }


        //Total mortgage with interest
        var tmwi = monthlyMortgage * mcAmortizationPeriod * 12;

        //Total with down payment
        var tmwdp = tmwi + mcDownPayment;

        // Getting localize php strings
        var outPutString = mc_strings.mc_output_string;

        //Currency sign
        var mcCurrencySign = mc_strings.mc_currency_sign;

        // Decimal numbers
        var decimalNumbers = mc_strings.mc_number_of_decimals;

        //Decimal Separator
        var decimalSeparator = mc_strings.mc_decimal_separator;

        //Thousand Separator
        var thousandSeparator = mc_strings.mc_thousand_separator;

        //Currency Sign Position
        var currencySignPosition = mc_strings.mc_currency_sign_position;

        //Formatting principal amount
        principal= number_format( principal, decimalNumbers, decimalSeparator, thousandSeparator );

        //Assigning currency sign position to principal
        principal = (currencySignPosition == 'before') ? mcCurrencySign+principal : principal+mcCurrencySign;

        //Formatting monthly mortgage amount
        monthlyMortgage= number_format( monthlyMortgage, decimalNumbers, decimalSeparator, thousandSeparator );

        //Assigning currency sign position to monthly Mortgage
        monthlyMortgage = (currencySignPosition == 'before') ? mcCurrencySign+monthlyMortgage : monthlyMortgage+mcCurrencySign;

        //Formatting monthly mortgage with interest amount
        tmwi= number_format( tmwi, decimalNumbers, decimalSeparator, thousandSeparator );

        //Assigning currency sign position to monthly mortgage with interest amount
        tmwi = (currencySignPosition == 'before') ? mcCurrencySign+tmwi : tmwi+mcCurrencySign;

        //Formatting total mortgage with down payment
        tmwdp= number_format( tmwdp, decimalNumbers, decimalSeparator, thousandSeparator );

        //Assigning currency sign position to total mortgage with down payment
        tmwdp = (currencySignPosition == 'before') ? mcCurrencySign+tmwdp : tmwdp+mcCurrencySign;

        outPutString = outPutString.replace( "[mortgage_amount]", principal);
        outPutString = outPutString.replace( "[amortization_years]", mcAmortizationPeriod );
        outPutString = outPutString.replace( "[mortgage_payment]", monthlyMortgage );
        outPutString = outPutString.replace( "[total_mortgage_interest]", tmwi );
        outPutString = outPutString.replace( "[total_mortgage_down_payment]", tmwdp );
        outPutString = outPutString + ' <a href="#" onclick="jQuery(\'#mc_mlc_form\').submit(); return false;">Amortization Schedule</a>';

        //Displaying output div
        outputDiv.html( "<p>"+outPutString+"</p>").stop(true, true).slideDown();
        outputDiv.html(outputDiv.html().replace(new RegExp("LINEBREAK","g"),"<br>"));
    }


    // Form validation and submission
    if ( jQuery().validate ) {
        $("#mc-form").validate({
            rules: {
                field: {
                    number: true,
                    min:0
                }
            },
            submitHandler: function() {
                mcOutputFunc();
            }
        });
    }

    // Amortization schedule
    var iframeHeight = 578;
    var mlcalcFrameIsShown = false;
    if(!$('#MLCalcHolder, #MLCalcShader, #MLCalcClose').length){
        $('body').prepend('<div id="MLCalcHolder"></div><div id="MLCalcShader"></div><div id="MLCalcClose" style="display:none">X</div>');
    };

    $("#mc_mlc_form").submit(function(){
        // Getting total amount value from user
        var mcTotalAmount = parseFloat( $("#mc-total-amount").val() );

        //Getting down payment value from user
        var mcDownPayment = parseFloat( $("#mc-down-payment").val() );

        //Getting interest rate value from user
        var mcInterestRate = parseFloat( $("#mc-interest-rate").val() );

        //Getting mortgage period value from user
        var mcAmortizationPeriod = parseFloat( $("#mc-mortgage-period").val() );

        //Calculating principal amount by subtracting down payment from total amount
        var principal = mcTotalAmount - mcDownPayment;

        $("#mc_mlc_form INPUT[name=ma]").val(mcTotalAmount);
        $("#mc_mlc_form INPUT[name=dp]").val(Number.parseInt(mcDownPayment/mcTotalAmount*100));
        $("#mc_mlc_form INPUT[name=mt]").val(mcAmortizationPeriod);
        $("#mc_mlc_form INPUT[name=ir]").val(mcInterestRate);
        initFloatLayer(iframeHeight);
    });

})(jQuery);

function initFloatLayer(iframeHeight){
    var viewportWidth  = jQuery(window).width();
    var viewportHeight = jQuery(window).height();

    var documentWidth  = 0;
    var documentHeight = 0;
    var viewportLeft   = 0;
    var viewportTop    = 0;

    if(document.body){
        documentWidth  = document.body.scrollWidth;
        documentHeight = document.body.scrollHeight;
        viewportLeft   = document.body.scrollLeft;
        viewportTop    = document.body.scrollTop;
    };
    if(document.documentElement){
        documentWidth  = Math.min(documentWidth, document.documentElement.scrollWidth);
        documentHeight = Math.max(documentHeight, document.documentElement.scrollHeight);
        viewportLeft   = Math.max(viewportLeft, document.documentElement.scrollLeft);
        viewportTop    = Math.max(viewportTop, document.documentElement.scrollTop);
    };

    var viewWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    var frameWidth = Math.min(viewWidth, 740);

    var shaderWidth = Math.max(documentWidth, viewportWidth);
    var shaderHeight = Math.max(documentHeight, viewportHeight);
    jQuery('#MLCalcShader')
        .css({
            width:shaderWidth,
            height:shaderHeight,
            top:0,
            left:0,
            opacity:'0.5'
        })
        .show()
        .click(function(){
            mlcalcHideAll();
        });

    var holderLeft = parseInt((viewportWidth - frameWidth) / 2) + viewportLeft;
    var holderTop  = parseInt((viewportHeight - iframeHeight) / 2) + viewportTop;
    if(holderLeft < 0) holderLeft = 0;
    if(holderTop < 0) holderTop = 0;
    mlcalcFrameIsShown = true;
    jQuery('#MLCalcHolder')
        .css({
            width:frameWidth,
            height:iframeHeight,
            top:holderTop,
            left:holderLeft
        })
        .show();

    if(jQuery('#MLCalcHolder #MLCalcFrame').length < 1){
        jQuery('#MLCalcHolder').html('<iframe src="#" scrolling="no" id="MLCalcFrame" name="MLCalcFrame" width="0" height="0" frameborder="0" allowtransparency="true" style="background-color: transparent; display: none"></iframe><iframe id="garbageFrame" style="display:none"></iframe>')
    };
    jQuery(document).keyup(function(e) {
        if (e.keyCode == 27) mlcalcHideAll();
    });
    jQuery('#MLCalcHolder').find('#MLCalcFrame').css({width:frameWidth, height:iframeHeight}).on('load', function(){
        jQuery(this).show();
        jQuery('#MLCalcHolder #garbageFrame').attr('src', '');
        jQuery('#MLCalcClose').show().css({height:25, width:25}).css({top:holderTop, left:holderLeft+jQuery('#MLCalcHolder').width()-2-jQuery('#MLCalcClose').width()})
            .click(function(){
                mlcalcHideAll();
            })
            .hover(function(){
                jQuery(this).css({background:'#F5F5F5', color:'#808080'});
            }, function(){
                jQuery(this).css({background:'#D5D5D5', color:'#F5F5F5'});
            });
    });
};
function mlcalcHideAll(){
    if(!mlcalcFrameIsShown) return false;
    mlcalcFrameIsShown = false;
    jQuery('#MLCalcShader').fadeOut(300);
    jQuery('#MLCalcHolder, #MLCalcClose').hide();
    jQuery('#MLCalcFrame').remove();
};