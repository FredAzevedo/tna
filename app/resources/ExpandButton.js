
function Expandir() {
    $(document).ready(function(){
        $("form .panel-body").toggleClass("collapse");
        $("#custom-id-botao").click(function(){
          event.preventDefault();
          $(".card-body.panel-body").toggleClass("collapse show");    
        });
    });
};

$(function(){
    Expandir();
});