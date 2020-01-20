// register JQuery Code

//this line of code, runs the document when it is ready to be run or page is loaded
$(document).ready(function() {
    //On click signup, hide login and show registration form
    //The # targets a html id, first id holds login html code 
    //NOTE: to target a class it would be .first
    $("#signup").click(function() {
        $("#first").slideUp("slow", function(){ //slideup and slidedown is jquery library code
            $("#second").slideDown("slow");
        });
    });   
    
     //On click signup, hide registration and show login form
    $("#signin").click(function() {
        $("#second").slideUp("slow", function(){ //slideup and slidedown is jquery library code
            $("#first").slideDown("slow");
        });
    });     
});