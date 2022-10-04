$(document).ready(function(){
    $('.load-more').click(function(){
        var totalDisplayTricks = Number($('#totalDisplayTricks').val());
        var totalAllTricks = Number($('#totalAllTricks').val());
        var tricksPerLoading = Number($('#tricksPerLoading').val());

        if(totalDisplayTricks <= totalAllTricks){
            $("#totalDisplayTricks").val(totalDisplayTricks + tricksPerLoading);

            $.ajax({
                url: '/getData',
                type: 'post',
                data: {totalDisplayTricks:totalDisplayTricks},
                beforeSend:function(){
                    $(".load-more").text("Chargement...");
                },
                success: function(response){

                    // Setting little delay while displaying new content
                    setTimeout(function() {
                        // Appending tricks after last trick with class="trick"
                        $(".trick:last").after(response).show().fadeIn("slow");

                        var totalDisplayTricks = Number($('#totalDisplayTricks').val());
                        // checking tricksNumber value is greater than totalTricks or not

                        if(totalDisplayTricks >= totalAllTricks){
                            // Change the text and background
                            $('.load-more').addClass('d-none');
                        }else{
                            $(".load-more").text("Afficher plus de figures");
                            if(totalDisplayTricks > 15) {
                                $("#arrow-top").show();
                            }
                        }
                    }, 400);
                }
            });
        }
    });
});

