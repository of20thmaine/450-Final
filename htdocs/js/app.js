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
                <textarea contenteditable="true" name="reply" form="`+ formID +`" placeholder="Please enter your reply..."></textarea>
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

    if (reply.length < 2) {
        alert("Reply must be longer than 1 character.");
        return false;
    } else if (reply.length > 30000) {
        alert("Character limit is 30,000; please shorten your post.");
        return false;
    } else if (newLineCount(reply) > 16) {
        alert("Replies may only contain 16 or less user inserted lines (enter key presses).");
        return false;
    }
    return true;
}

function newLineCount(reply) {
    let count = 0;
    for (let i = 0; i < reply.length; ++i) {
        if (reply.charAt(i) === '\n' || reply.charAt(i) === '\r') {
            count++;
        }
    }
    return count;
}

function toggleSideMenu() {
    let targetDiv = document.getElementsByClassName('sidemenu')[0];

    if (targetDiv.style.display === "block") {
        targetDiv.style.display = "none";
    } else {
        targetDiv.style.display = "block";
    }
}

function toggleHiddenMenu() {
    let targetDiv = document.getElementsByClassName('hidden-sub')[0];

    if (targetDiv.style.display === "block") {
        targetDiv.style.display = "none";
    } else {
        targetDiv.style.display = "block";
    }
}
