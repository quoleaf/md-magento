

$(document).ready(function(){
     
    $('#next1').click(function(){
        var selValue = '#'+ $('input[name=q1]:checked').val();
        if(selValue !== "#undefined"){
            $('#popupQuestion').addClass('lity-hide');
            $('#popupQuestion').css('display','none');
            $(selValue).addClass('lity lity-opened lity-inline').css('display','flex');
            $('.pleaseSelect').css('visibility', 'hidden');
            $(this).closest('.img-label').addClass('highlight');
        }
        else{
            $('#popupQuestion .pleaseSelect').css('visibility', 'visible');
        }
    });

    $('#next-hair').click(function(){
        var selValue2 = '#'+ $('input[name=q2]:checked').val();
        if(selValue2 !== "#undefined"){
            $('#hairloss').addClass('lity-hide').css('display','none');
            $('#popupQuestion-thankYou').addClass('lity lity-opened lity-inline').css('display','flex');
            $('.pleaseSelect').css('visibility', 'hidden');
        }
        else{
            $('.pleaseSelect').css('visibility', 'visible');
        }
    });

    
    $('#next-lash').click(function(){
        var selValue3 = '#'+ $('input[name=q3]:checked').val();
        if(selValue3 !== "#undefined"){
            $('#lash').addClass('lity-hide').css('display','none');
            $('#popupQuestion-thankYou').addClass('lity lity-opened lity-inline').css('display','flex');
            $('.pleaseSelect').css('visibility', 'hidden');
        }
        else{
            $('.pleaseSelect').css('visibility', 'visible');
        }
    });

    $('#next-skin').click(function(){
        var selValue4 = '#'+ $('input[name=q4]:checked').val();
        if(selValue4 !== "#undefined"){
            $('#skin').addClass('lity-hide').css('display','none');
            $('#popupQuestion-thankYou').addClass('lity lity-opened lity-inline').css('display','flex');
            $('.pleaseSelect').css('visibility', 'hidden');
        }
        else{
            $('.pleaseSelect').css('visibility', 'visible');
        }
    });

    $('#next-welness').click(function(){
        var selValue5 = '#'+ $('input[name=q5]:checked').val();
        if(selValue5 !== "#undefined"){
            $('#welness').addClass('lity-hide').css('display','none');
            $('#popupQuestion-thankYou').addClass('lity lity-opened lity-inline').css('display','flex');
            $('.pleaseSelect').css('visibility', 'hidden');
        }
        else{
            $('.pleaseSelect').css('visibility', 'visible');
        }
    });


    $('#close').click(function(){
        $('#popupQuestion').addClass('lity-hide').css('display','none');
        $('#skinproblem').addClass('lity-hide').css('display','none');
        $('#lash').addClass('lity-hide').css('display','none');
        $('#hairloss').addClass('lity-hide').css('display','none');
        $('#popupQuestion-thankYou').addClass('lity-hide').css('display','none');
    });

    $('#back1').click(function(){
        $('#popupQuestion').addClass('lity lity-opened lity-inline').css('display','flex');
        $('#hairloss').addClass('lity-hide').css('display','none');
    });

    $('#back2').click(function(){
        $('#popupQuestion').addClass('lity lity-opened lity-inline').css('display','flex');
        $('#lash').addClass('lity-hide').css('display','none');
    });

    $('#back3').click(function(){
        $('#popupQuestion').addClass('lity lity-opened lity-inline').css('display','flex');
        $('#skinproblem').addClass('lity-hide').css('display','none');
    });

    $('#back4').click(function(){
        $('#popupQuestion').addClass('lity lity-opened lity-inline').css('display','flex');
        $('#welness').addClass('lity-hide').css('display','none');
    });

});

       