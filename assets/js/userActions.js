//Following functionality
function follow(userTo, userFrom, button) {
    if(userTo == userFrom) {
        alert("You can't follow yourself");
        return;
    }

    //Ajax call
    $.post("includes/handlers/follow.php", { userTo: userTo, userFrom: userFrom })
    .done(function(count) {
        if(count != null) {
            $(button).toggleClass("follow unfollow");

            var buttonText = $(button).hasClass("follow") ? "FOLLOW" : "FOLLOWING";
            $(button).text(buttonText + " " + count);
        }
        //Error might of occured
        else {
            alert("Something may have gone wrong");
        }
    });
}