var session = {
    connection: null,
    user: "sarah@hayhay",
    pass: "a",
    partner: null,
    thread: null,
    here: null,
    composing: null,

    jid_to_id: function (jid) {
        return Strophe.getBareJidFromJid(jid)
            .replace(/@/g, "-")
            .replace(/\./g, "-");
    },

    on_message: function (message) {
        var full_jid = $(message).attr('from');
        var jid = Strophe.getBareJidFromJid(full_jid);
        session.partner = jid;
        dispPartner();
        var jid_id = session.jid_to_id(jid);

        var composing = $(message).find('composing');
        if (composing.length > 0) {
            // handle composing
        }

        var body = $(message).find("html > body");

        if (body.length === 0) {
            body = $(message).find('body');
            if (body.length > 0) {
                body = body.text()
            } else {
                body = null;
            }
        } else {
            body = body.contents();

            var span = $("<span></span>");
            body.each(function () {
                if (document.importNode) {
                    $(document.importNode(this, true)).appendTo(span);
                } else {
                    // IE workaround
                    span.append(this.xml);
                }
            });

            body = span;
        }

        if (body) {
            $(".messages").append(makeOtherRow(body));
        }

        return true;
    },

    scroll_chat: function (jid_id) {
        var div = $('#chat-' + jid_id + ' .chat-messages').get(0);
        div.scrollTop = div.scrollHeight;
    },
};

$(document).ready(function () {
    $(document).trigger('connect');

    $('.chat-input').keypress( function (ev) {
        var jid = session.partner;

        if (ev.which === 13) {
            ev.preventDefault();

            var body = $(this).val();

            var message = $msg({to: jid,
                "type": "chat"})
                .c('body').t(body).up()
                .c('active', {xmlns: "http://jabber.org/protocol/chatstates"});
            session.connection.send(message);
            $(".messages").append(makeMyRow(body));

            $(this).val('');
        } else {
            var composing = $(this).parent().data('composing');
            if (!composing) {
                var notify = $msg({to: jid, "type": "chat"})
                    .c('composing', {xmlns: "http://jabber.org/protocol/chatstates"});
                session.connection.send(notify);

                session.composing = true;
            }
        }
    });

    $('#disconnect').click(function () {
        session.connection.disconnect();
        session.connection = null;
    });

    $('#chat').click(function () {
        var jid = $('#chat-jid').val().toLowerCase();
        var jid_id = session.jid_to_id(jid);
        session.partner = jid;
        dispPartner();
    });
});

function makeMyRow(body) {
    return "<div class='mychatrow'><p class='message'>" + body + "</p><p class='timestamp'>"+ formattedTime() + "</p>";
};

function makeOtherRow(body) {
    return "<div class='chatrow'><p class='message'>" + body + "</p><p class='timestamp'>" + formattedTime() + "</p>";
}

function formattedTime() {
    var time = new Date($.now());
    return time.toLocaleString();
}

function dispPartner() {
    if (session.partner != null) {
        $("#chatTitle").html("chatting with " + session.partner);
    } else if (session.thread != null) {
        $("#chatTitle").html("chatting in the " + session.thread + " thread");
    }
}

function send_msg(msg, type) {
    var url = "http://www.nimitae.sg/hayhay/server/message.php";
    var other;
    if ( type == 1) {
        // pri
        other = session.partner;
    } else {
        // pub
        other = session.thread;
    }
    $.ajax({
        url: url,
        type: "POST",
        data: {username: session.user, type: type, message: msg, receiver: other},
        success: sent,
        error: whoops
    });
}

function sent() {
    // acknowledge it somehow
};

function newThread(title, range) {
    var url = "http://www.nimitae.sg/hayhay/server/create.php";
    $.ajax({
        url: url,
        type: "POST",
        data: {title: title, longitude: session.here.longitude, latitude: session.here.latitude, username: session.user},
        success: newThr,
        error: whoops
    });
}

function newThr(threadID) {
    session.thread = threadID;
    session.partner = null;
};

function list() {
    var url = "http://www.nimitae.sg/hayhay/server/listing.php";
    $.ajax({
        url: url,
        type: "POST",
        data: {longitude: session.here.longitude, latitude: session.here.latitude, range: session.range},
        success: listing,
        error: whoops
    });
}

function listing(threads) {

}

$(document).bind('connect', function () {
    var conn = new Strophe.Connection(
        'http://www.nimitae.sg/xmpp-httpbind/');
    conn.connect(session.user, session.pass, function (status) {
        if (status === Strophe.Status.CONNECTED) {
            $(document).trigger('connected');
        } else if (status === Strophe.Status.DISCONNECTED) {
            $(document).trigger('disconnected');
        }
    });

    session.connection = conn;
    session.pass = null;
});

$(document).bind('connected', function () {
    var iq = $iq({type: 'get'}).c('query', {xmlns: 'jabber:iq:roster'});
    session.connection.send($pres());

    session.connection.addHandler(session.on_message,
        null, "message", "chat");
});

$(document).bind('disconnected', function () {
    session.connection = null;
});

function whoops(xhr, ajaxOptions, throwError) {
    alert("no");
    alert(xhr.status);
    alert(xhr.responseText);
};