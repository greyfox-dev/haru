define( function () {
    function run() {
    	// Delete existing footnote entries in case we're reloading the
        // footnodes.
        var i;
        var noteholder = document.getElementById("footnotes");
        if (!noteholder) {
            return;
        }
        var entriesToRemove = [];
        for (i = 0; i < noteholder.childNodes.length; i++) {
            var entry = noteholder.childNodes[i];
            if (entry.nodeName == 'div'
            	&& entry.getAttribute("class") == "footnote")
                entriesToRemove.push(entry);
        }
        for (i = 0; i < entriesToRemove.length; i++) {
            noteholder.removeChild(entriesToRemove[i]);
        }

        // Rebuild footnote entries.
        var cont = document.getElementById("content");
        var spans = cont.getElementsByTagName("span");
        var refs = {};
        var n = 0;
        for (i = 0; i < spans.length; i++) {
            if (spans[i].className == "footnote") {
                n++;
                var note = spans[i].getAttribute("data-note");
                if (!note) {
                    // Use [\s\S] in place of . so multi-line matches work.
                    // Because JavaScript has no s (dotall) regex flag.
                    note = spans[i].innerHTML.match(/\s*\[([\s\S]*)]\s*/)[1];
                    spans[i].innerHTML = "[<a id='_footnoteref_" + n
                            + "' href='#_footnote_" + n
                            + "' title='View footnote' class='footnote'>" + n
                            + "</a>]";
                    spans[i].setAttribute("data-note", note);
                }
                noteholder.innerHTML += "<div class='footnote' id='_footnote_"
                        + n + "'>" + "<a href='#_footnoteref_" + n
                        + "' title='Return to text'>" + n + "</a>. " + note
                        + "</div>";
                var id = spans[i].getAttribute("id");
                if (id != null)
                    refs["#" + id] = n;
            }
        }
        if (n == 0)
            noteholder.parentNode.removeChild(noteholder);
        else {
            // Process footnoterefs.
            for (i = 0; i < spans.length; i++) {
                if (spans[i].className == "footnoteref") {
                    var href = spans[i].getElementsByTagName("a")[0]
                            .getAttribute("href");
                    href = href.match(/#.*/)[0]; // Because IE return full
                    // URL.
                    n = refs[href];
                    spans[i].innerHTML = "[<a href='#_footnote_" + n
                            + "' title='View footnote' class='footnote'>" + n
                            + "</a>]";
                }
            }
        }
    }

    return {
    	run: run
    }
})