function displayReplyBox(id, fp, location, topic) {
    let targetDiv = document.getElementById(id).getElementsByClassName("reply-area")[0];
    let formID = "reply_form"+id;

    if (targetDiv.style.display === "inline-block") {
        targetDiv.innerHTML = "";
        targetDiv.style.display = "none";
        return;
    } else {
        targetDiv.style.display = "inline-block";
    }

    targetDiv.innerHTML = `
            <div class="reply-box">
                <textarea name="reply" form="`+ formID +`" placeholder="Please enter your reply..."></textarea>
            </div>
            <form action="interaction.php" id="`+ formID +`" onsubmit="return validateReply('`+formID+`')" method="post">
                <input type="hidden" id="postId" name="postId" value="`+ id.slice(1) +`">
                <input type="hidden" name="location" value="`+ location +`">
                <input type="hidden" name="fp" value="`+ fp +`">
                <input type="hidden" name="topic" value="`+ topic +`">
                <input type="submit" value="Reply">
            </form>`;
}

function validateReply(id) {
    let reply = document.getElementById(id).elements["reply"].value;
    // let reply = document.forms["reply_form"+id]["reply"].value;
    if (reply.length < 2) {
        alert("Reply must be longer than 1 character.");
        return false;
    }
    return true;
}

function sideMenuToggle(id) {
    let targetDiv = document.getElementsByClassName("sidemenu")[0];


    if (targetDiv.style.display === "block") {
        targetDiv.style.display = "none";
        id.classList.remove("fa-times");
        id.classList.add("fa-bars");
    } else {
        targetDiv.style.display = "block";
        id.classList.remove("fa-bars");
        id.classList.add("fa-times");
    }
}

function getWidth() {
    if (self.innerWidth) {
        return self.innerWidth;
    }

    if (document.documentElement && document.documentElement.clientWidth) {
        return document.documentElement.clientWidth;
    }

    if (document.body) {
        return document.body.clientWidth;
    }
}