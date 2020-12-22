$(document).ready(function() {
    $('#hit').click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: '/blackjack',
            data: "hit",
            success: function(response)
            {
                location.reload();
           }
       });
     });
	 
	 $('#stand').click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: '/blackjack',
            data: "stand",
            success: function(response)
            {
                location.reload();
           }
       });
     });
	 
	 $('#fold').click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: '/blackjack',
            data: "fold",
            success: function(response)
            {
                location.reload();
           }
       });
     });
});
