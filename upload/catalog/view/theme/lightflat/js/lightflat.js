//quantity in the product card
$(document).ready(function(){
    $('.minus_quantity').click(function(){
        var $input = $(this).parent().find('.select_quantity');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false
    });
    $('.plus_quantity').click(function(){
        var $input = $(this).parent().find('.select_quantity');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false 
    });
});
    //button scroll up
$(function() {
    var pageYLabel = 0;
    $(window).load(function(){
      var pageY = $(window).scrollTop();
      if (pageY > 100) {
        $('#up').show();
      }
    })
    .scroll(function(e){
      var pageY = $(window).scrollTop();
      var innerHeight = $(window).innerHeight();
      var docHeight = $(document).height();
      if (pageY > innerHeight) {
        $('#up').show();
      } else {
        $('#up').hide();
      }
    });
    $('#up').click(function() {
      var pageY = $(window). scrollTop();
      pageYLabel = pageY;
      $('html,body').animate({scrollTop:0}, 'slow');
    });
  });